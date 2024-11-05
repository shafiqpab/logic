<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');
$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$department_name_library=return_library_array( "select id,department_name from lib_department", "id", "department_name"  );
$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$deal_merchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:580px;">
                <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Job No</th>
                        <th>
                            <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">							
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" /> 
                        </th> 
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
								<? 
                                echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                ?>
                            </td>                 
                            <td align="center">	
								<?
                                $search_by_arr=array(1=>"Job No",2=>"Style Ref");
                                $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
                                echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                ?>
                            </td>     
                            <td align="center" id="search_by_td">				
                            	<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                            </td> 	
                            <td align="center">
                            	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'cpa_short_fabric_booking_analysis_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:15px" id="search_div"></div>
            </fieldset>
        </form>
        </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}	
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($data[2]==1)
	{
		if($data[3]!="") $search_field_cond=" and job_no_prefix_num='$data[3]'"; else $search_field_cond="";
	}
	else if($data[2]==2)
	{
		if($data[3]!="") $search_field_cond="and style_ref_no like '$search_string'"; else $search_field_cond="";
	}
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and YEAR(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond="and to_char(insert_date,'YYYY')=$year_id"; else $year_cond="";	
	}
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $search_field_cond $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','','') ;
	exit(); 
} // Job Search end

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
	<script>
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			//alert(booking_no);
			parent.emailwindow.hide();
		}
		
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <tr>
                <td align="center" width="100%">
                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                        <thead>                	 
                        	<th width="150">Company Name</th>
                            <th width="140">Buyer Name</th>
                            <th width="80">Short Booking No</th>
                            <th width="180">Short Booking Date</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tr>
                            <td>
                                <input type="hidden" id="selected_booking">
                                <input type="hidden" id="job_no" value="<? echo $data[2];?>">
                                <? 
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0], "load_drop_down( 'cpa_short_fabric_booking_analysis_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --" ); ?></td>
                            <td>
                                <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('txt_booking_no').value, 'create_booking_search_list_view', 'search_div', 'cpa_short_fabric_booking_analysis_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                             </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                	<? 
					echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
					echo load_month_buttons();  ?>
                </td>
            </tr>
            <tr>
                <td align="center"valign="top" id="search_div"></td>
            </tr>
        </table>    
	</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> load_drop_down( 'cpa_short_fabric_booking_analysis_report_controller','<? echo $data[0];?>', 'load_drop_down_buyer', 'buyer_td' );
	</script>
	</html>
	<?
	exit(); 
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";
	if ($data[4]!=0) $job_no=" and job_no='$data[4]'"; else $job_no='';
	if ($data[5]!=0) $booking_no=" and booking_no_prefix_num='$data[5]'"; else $booking_no='';
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$po_array=array();
	$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type=1 and is_short=1 and status_active=1  and 	is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		//print_r( $po_id);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$order_arr[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
	
	$sql= "select booking_no_prefix_num, booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, supplier_id, is_approved, ready_to_approved from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type=1 and is_short=1 and status_active=1 and is_deleted=0 order by id Desc";
	 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0','','');
	
	exit(); 
}

	$tmplte=explode("**",$data);
	if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
	if ($template=="") $template=1;
	
if ($action=="cpa_report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_division_id=str_replace("'","",$cbo_division_id);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$date_cond='';
	if(str_replace("'","",$cbo_search_date)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
		 	if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}
	else if(str_replace("'","",$cbo_search_date)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
		 	if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and c.booking_date between '$start_date' and '$end_date'";
		}
	}
	else if(str_replace("'","",$cbo_search_date)==3)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
		 	if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and e.country_ship_date between '$start_date' and '$end_date'";
		}
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	$booking_no=str_replace("'","",$txt_booking_no);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_short_booking_type=str_replace("'","",$cbo_short_booking_type);
	
	$cbo_ref=str_replace("'","",$txt_ref_no);
	$cbo_file=str_replace("'","",$txt_file_no);
	
	$cbo_booking_type=str_replace("'","",$cbo_booking_type);
	if ($cbo_booking_type==0) $cbo_booking_type_cond="and c.is_approved in (0,1)"; else $cbo_booking_type_cond=" and c.is_approved='$cbo_booking_type'";
	if ($cbo_division_id==0) $division_cond=""; else $division_cond=" and d.division_id in($cbo_division_id)";
	if ($cbo_short_booking_type==0) $short_booking_type_cond=""; else $short_booking_type_cond=" and c.short_booking_type in($cbo_short_booking_type)";
	
	//echo $buyer_id_cond.'='.$end_date;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num='$job_no'";
	if ($booking_no=="") $booking_no_cond=""; else $booking_no_cond=" and c.booking_no_prefix_num in ($booking_no)";
	//kaiyum
	
	if ($cbo_ref=="") $ref_no_cond=""; else $ref_no_cond=" and b.grouping='$cbo_ref'";
	if ($cbo_file=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$cbo_file'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	$year_cond="";
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		//else $year_cond="";
	} 
	$search_date_type=str_replace("'","",$cbo_search_date);
	
	if(str_replace("'","",$chk_date_range)==1) $inc_date_range="and c.is_short=1"; else $inc_date_range="";
	if($template==1)
	{
		ob_start();
		$style1="#E9F3FF"; $style="#FFFFFF";
		$company_name=str_replace("'","",$cbo_company_name);
		$search_by = array(1=>'Shipment Date',2=>'Booking Date',3=>'Country Ship Date');
		if($rpt_type==1)
		{
		?>
		<div style="width:2010px;" id="content_search_panel2">
		<fieldset style="width:100%;">	
            <table width="2110">
                <tr class="form_caption">
                    <td colspan="23" align="center"><strong><? echo $report_title; if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo '; '.$search_by[str_replace("'","",$cbo_search_date)].' From : '.change_date_format(str_replace("'","",$txt_date_from)).' To : '.change_date_format(str_replace("'","",$txt_date_to)) ?></strong></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="23" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                </tr>
            </table>
            <table id="table_header_1" class="rpt_table" width="2110" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Year</th>
                        <th width="100">Job No</th>
                        <th width="100">Buyer</th>
                        
                        <th width="100">Ref No</th>
                        <th width="100">File No</th>
                        
                        <th width="100">Booking Date</th>
                        <th width="100">Booking No</th>
                        <th width="100">Order No</th>
                        <th width="100">Fabric Color</th>
                        <th width="70">Order Qty(pcs)</th>
                        <th width="80">Main Booking Qty</th>
                        <th width="70">CPA Grey Qty(kg)</th>
                        <th width="80">CPA Finish (Qty)</th>
                        <th width="80">Total Grey Qty</th>
                        <th width="80">Pre-Costing Cons/Dzn</th>
                        <th width="80">Quote Cons/Dzn</th>
                        <th width="80">Total Booking Con's/Doz</th>
                        <th width="80">Pre Cost Variance</th>
                        <th width="80">Quote Variance</th>
                        <th width="70">Excess Fabric(%)</th>
						<th width="100">Division</th>
                        <th width="90">Responsible Dept.</th>
                        <th width="80">Responsible</th>
                        <th>Reason</th>
                    </tr>
                </thead>
            </table>
            <div style="width:2130px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="2110" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?	
            $fabriccostArray=array();$date_string_arr=array(); $grey_qty_arr=array(); $responsible_dept_arr=array(); $responsible_person_arr=array(); $reason_arr=array();
            $booking_no_arr=array(); $short_booking_no_arr=array(); $booking_no_large_arr=array();$booking_qty_arr=array();
            $price_quotation_arr=array(); $price_qou_costArray=array(); $booking_dtls_arr=array(); $is_app_arr=array();
            $pq_sql=sql_select("select quotation_id,fab_knit_req_kg from wo_pri_quo_sum_dtls where status_active=1 and is_deleted=0  ");
            foreach($pq_sql as $row_pq)
            {
            	$price_quotation_arr[$row_pq[csf('quotation_id')]]['fab_knit_req']=$row_pq[csf('fab_knit_req_kg')];
            } //var_dump( $price_quotation_arr);
			unset($pq_sql);
            $price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
            foreach($price_costDataArray as $pri_fabRow)
            {
            	$price_qou_costArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
            } //var_dump( $price_qou_costArray);
			unset($price_costDataArray);
            $fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  ");
            foreach($fabriccostDataArray as $fabRow)
            {
            	$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
            } 
			unset($fabriccostDataArray);
            $sql_req_qty=sql_select("select job_no,fab_knit_req_kg from  wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0 ");
            foreach($sql_req_qty as $row_data)
            {
            	$grey_qty_arr[$row_data[csf('job_no')]]['fab_knit']=$row_data[csf('fab_knit_req_kg')];
            } //var_dump($grey_qty_arr);
			unset($sql_req_qty);
            $s_fab_book_arr=array();
            $item_fab_book_arr=array();
            $sfab_sql=sql_select("select c.job_no,c.fabric_source as short_fabric_source,c.item_category as short_item_category,d.division_id  from wo_po_details_master a, wo_booking_mst c,wo_booking_dtls d where c.booking_no=d.booking_no and c.booking_type=1 and c.is_short=1 and  a.job_no=c.job_no and c.company_id='$company_name' $job_no_cond and c.status_active=1 and c.is_deleted=0  $short_booking_type_cond");
            foreach($sfab_sql as $s_data)
            {
				$s_fab_book_arr[$s_data[csf('job_no')]]['short_fabric_source']=$s_data[csf('short_fabric_source')];
				$s_fab_book_arr[$s_data[csf('job_no')]]['short_item_category']=$s_data[csf('short_item_category')];
				if($s_data[csf('division_id')]!=0)
				{
					$s_fab_book_arr[$s_data[csf('job_no')]]['division_id'].=$short_division_array[$s_data[csf('division_id')]].',';
				}
            }// var_dump($s_fab_book_arr);
			unset($sfab_sql);
            $sm_fab_book_arr=array();
            $fab_sql=sql_select("select c.job_no,c.fabric_source as main_fabric_source,c.item_category as main_item_category from wo_po_details_master a, wo_booking_mst c,wo_booking_dtls d where c.booking_no=d.booking_no and c.booking_type=1 and c.is_short=2 and  a.job_no=c.job_no and c.company_id='$company_name' $job_no_cond and c.status_active=1 and c.is_deleted=0  ");
            foreach($fab_sql as $sm_data)
            {
				$sm_fab_book_arr[$sm_data[csf('job_no')]]['main_fabric_source']=$sm_data[csf('main_fabric_source')];
				$sm_fab_book_arr[$sm_data[csf('job_no')]]['main_item_category']=$sm_data[csf('main_item_category')];
            }  //var_dump($sm_fab_book_arr);
			unset($fab_sql);
			
			$sql_b_date=sql_select("select a.job_no,b.po_number, c.booking_date, d.responsible_dept, d.responsible_person, d.reason, c.is_short,  
				c.booking_no_prefix_num, c.booking_no, c.is_approved, d.fabric_color_id, d.po_break_down_id, d.grey_fab_qnty, d.fin_fab_qnty,b.po_quantity,a.total_set_qnty
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d 
				where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and 
				b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type=1 and a.company_name='$company_name' and 
				a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and 
				c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $buyer_id_cond $job_no_cond $year_cond $cbo_booking_type_cond $division_cond");
					
            foreach($sql_b_date as $row_date)
            {
				$responsible_dept_arr[$row_date[csf('job_no')]].=$row_date[csf('responsible_dept')].',';	
				$responsible_person_arr[$row_date[csf('job_no')]].=$row_date[csf('responsible_person')].',';	
				$reason_arr[$row_date[csf('job_no')]].=$row_date[csf('reason')].',';
				if(str_replace("'","",$row_date[csf('booking_no')])!="")
				{
					$booking_date=change_date_format($row_date[csf('booking_date')]);
					$bookdate = strtotime($booking_date); 
					$dateFrom = strtotime(change_date_format(str_replace("'","",$txt_date_from))); 
					$dateTo = strtotime(change_date_format(str_replace("'","",$txt_date_to))); 
					if($row_date[csf('is_short')]==1)
					{
						 //die;
						if(str_replace("'","",$chk_date_range)==1)
						{
							//echo $bookdate.'=='.$dateFrom.'=='.$dateTo.'<br>';
							//if($booking_date >= change_date_format(str_replace("'","",$txt_date_from)) && $booking_date <= change_date_format(str_replace("'","",$txt_date_to)))
							if(($bookdate >= $dateFrom) && ($bookdate <= $dateTo))
							{
								//echo change_date_format(str_replace("'","",$txt_date_from)).'=='.change_date_format(str_replace("'","",$txt_date_to)).'=='.$booking_date;
								$booking_no_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no_prefix_num')].',';
								$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['grey']+=$row_date[csf('grey_fab_qnty')];
								$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['fin']+=$row_date[csf('fin_fab_qnty')];
								$date_string_arr[$row_date[csf('job_no')]].=$row_date[csf('booking_date')].',';	
								$booking_no_large_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no')].',';
								$is_app_arr[$row_date[csf('booking_no_prefix_num')]][$row_date[csf('is_short')]]=$row_date[csf('is_approved')];
							}
						}
						else
						{
							$booking_no_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no_prefix_num')].',';
							$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['grey']+=$row_date[csf('grey_fab_qnty')];
							$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['fin']+=$row_date[csf('fin_fab_qnty')];
							$date_string_arr[$row_date[csf('job_no')]].=$row_date[csf('booking_date')].',';	
							$booking_no_large_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no')].',';
							$is_app_arr[$row_date[csf('booking_no_prefix_num')]][$row_date[csf('is_short')]]=$row_date[csf('is_approved')];
						}
					}
					else if($row_date[csf('is_short')]==2)
					{
						$booking_no_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no_prefix_num')].',';
						$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['grey']+=$row_date[csf('grey_fab_qnty')];
						$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['fin']+=$row_date[csf('fin_fab_qnty')];
						$date_string_arr[$row_date[csf('job_no')]].=$row_date[csf('booking_date')].',';	
						$booking_no_large_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no')].',';
						$is_app_arr[$row_date[csf('booking_no_prefix_num')]][$row_date[csf('is_short')]]=$row_date[csf('is_approved')];
					}
					
					$booking_po_arr[$row_date[csf('po_break_down_id')]]['po_no']=$row_date[csf('po_number')];
					$booking_dtls_arr[$row_date[csf('job_no')]]['po_id'].=$row_date[csf('po_break_down_id')].',';
					$booking_dtls_arr[$row_date[csf('job_no')]]['color_id'].=$row_date[csf('fabric_color_id')].',';
					
					$po_pcs_qty_arr[$row_date[csf('job_no')]][$row_date[csf('po_break_down_id')]]=$row_date[csf('po_quantity')]*$row_date[csf('total_set_qnty')];
					$po_qty_arr[$row_date[csf('job_no')]][$row_date[csf('po_break_down_id')]]=$row_date[csf('po_quantity')];
					
					/*$ex_short_booking_no=implode(",",array_unique(explode(',',$row_date[csf('short_booking_no')])));
					$short_booking_no_arr[$row_date[csf('job_no')]]=$ex_short_booking_no;	
					$ex_short_booking_large=implode(",",array_unique(explode(',',$row_date[csf('short_booking_large')])));
					$short_booking_no_large_arr[$row_date[csf('job_no')]]=$ex_short_booking_large;	*/
				}
            } 
         	 
			unset($sql_b_date);
			/*$booking_dtls_arr=array();
			$dtls_sql="select fabric_color_id, po_break_down_id, job_no from wo_booking_dtls where status_active=1 and is_deleted=0";
			$dtls_sql_result=sql_select($dtls_sql);
			foreach($dtls_sql_result as $drow )
			{
				$booking_dtls_arr[$drow[csf('job_no')]]['po_id']=$drow[csf('po_break_down_id')];
				$booking_dtls_arr[$drow[csf('job_no')]]['color_id']=$drow[csf('fabric_color_id')];
			}
			unset($dtls_sql_result);*/
            $i=1;
			if($db_type==0)
            {
				/*$sql="select a.job_no, YEAR(a.insert_date) as year, a.job_no_prefix_num, a.company_name, a.total_set_qnty as ratio, a.buyer_name, a.quotation_id,
				sum(case c.is_short when 2 then d.grey_fab_qnty end) as main_booking_qty,
				sum(case c.is_short when 1 then d.grey_fab_qnty end) as cpa_grey_qty,
				sum(case c.is_short when 1 then d.fin_fab_qnty end) as cpa_fin_qty,
				sum(d.grey_fab_qnty) as grey_fab_qnty,
				sum(distinct b.po_quantity) as po_quantity 
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, wo_po_color_size_breakdown e
				where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and b.id=e.po_break_down_id and c.booking_type=1 and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_cond $buyer_id_cond $job_no_cond $booking_no_cond $year_cond 
				group by a.job_no, a.job_no_prefix_num, a.company_name, a.total_set_qnty,a.buyer_name, a.insert_date, a.quotation_id order by a.job_no_prefix_num DESC";*/
				$sql="select a.job_no, YEAR(a.insert_date) as year, a.job_no_prefix_num, a.company_name, a.total_set_qnty as ratio, a.buyer_name, a.quotation_id,
					sum(distinct b.po_quantity) as po_quantity, b.grouping, b.file_no  
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, wo_po_color_size_breakdown e
				where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and b.id=e.po_break_down_id and c.booking_type=1 and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_cond $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $ref_no_cond $file_no_cond $cbo_booking_type_cond $inc_date_range $division_cond $short_booking_type_cond
				group by a.job_no, a.job_no_prefix_num, a.company_name, a.total_set_qnty, a.buyer_name, a.insert_date, a.quotation_id, a.job_quantity,b.grouping,b.file_no order by a.job_no_prefix_num DESC";// and c.is_short=1
            }
            else if($db_type==2)
            {
				$sql="select a.job_no, to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num, a.company_name, a.total_set_qnty as ratio, a.buyer_name, a.quotation_id,
					sum(distinct b.po_quantity) as po_quantity, b.grouping, b.file_no  
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, wo_po_color_size_breakdown e
				where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and b.id=e.po_break_down_id and c.booking_type=1  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_cond $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $ref_no_cond $file_no_cond $cbo_booking_type_cond $inc_date_range $division_cond $short_booking_type_cond
				group by a.job_no, a.job_no_prefix_num, a.company_name, a.total_set_qnty, a.buyer_name, a.insert_date, a.quotation_id, a.job_quantity, b.grouping, b.file_no order by a.job_no_prefix_num DESC";//and c.is_short=1
            }
			// echo $sql;
			/*sum(case c.is_short when 2 then d.grey_fab_qnty end) as main_booking_qty,
					sum(case c.is_short when 1 then d.grey_fab_qnty end) as cpa_grey_qty,
					sum(case c.is_short when 1 then d.fin_fab_qnty end) as cpa_fin_qty,
			LISTAGG(CAST(d.fabric_color_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY d.fabric_color_id) as fabric_color_id ,
			LISTAGG(CAST(d.po_break_down_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY d.po_break_down_id) as po_break_down_id,*/
			//echo  $sql;
            $result=sql_select($sql);
            $tot_rows=count($result);
            foreach($result as $row )
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$main_booking_no_prefix=implode(",",array_unique(explode(',',$booking_no_arr[$row[csf('job_no')]][2])));
				$short_booking_no_prefix=implode(",",array_unique(explode(',',$booking_no_arr[$row[csf('job_no')]][1])));
				
				$main_booking_no_large=implode(",",array_unique(explode(',',$booking_no_large_arr[$row[csf('job_no')]][2])));
				$short_booking_no_large=implode(",",array_unique(explode(',',$booking_no_large_arr[$row[csf('job_no')]][1])));
				//print_r($main_booking_no_prefix);
				//echo $short_booking_no_large.'='.$main_booking_no_large.'<BR>';
				$main_booking_data=explode(',', $main_booking_no_prefix); 
				$short_booking_data=explode(",",$short_booking_no_prefix);
				
				$main_booking_large_data=explode(',', $main_booking_no_large); 
				$short_booking_large_data=explode(",",$short_booking_no_large);
				//print_r($booking_no_large_arr[$row[csf('job_no')]][1]).'<br>';
				//echo $short_booking_no_large.'<BR>';
				$main_booking_no_large_arr=array();
				foreach($main_booking_large_data as $mb_large)
				{	
					$ex_mb=explode('-',$mb_large);
					//echo ltrim($ex_short[3],'0').'<BR>';
					$main_booking_no_large_arr[ltrim($ex_mb[3],'0')]=$mb_large;
					/*if($mb_large>0)
					{
						if($main_booking_no_large_data=="") $main_booking_no_large_data=$mb_large; else $main_booking_no_large_data.=",".$mb_large;
					}*/
				}
				$short_booking_no_large_arr=array();
				foreach($short_booking_large_data as $sb_large)
				{	//echo $sb_large.'<br>kkk';
					$ex_short=explode('-',$sb_large);
					//echo ltrim($ex_short[3],'0').'<BR>';
					$short_booking_no_large_arr[ltrim($ex_short[3],'0')]=$sb_large;
					/*if($sb_large!="")
					{
						if($short_booking_no_large_data=="") $short_booking_no_large_data=$sb_large; else $short_booking_no_large_data.=",".$sb_large;
					}*/
				}
				//print_r($short_booking_no_large_arr);
				$main_fab_source=$sm_fab_book_arr[$row[csf('job_no')]]['main_fabric_source'];
				$short_fab_source=$s_fab_book_arr[$row[csf('job_no')]]['short_fabric_source'];
				$division_id=rtrim($s_fab_book_arr[$row[csf('job_no')]]['division_id'],',');
				$main_fab_item_category=$sm_fab_book_arr[$row[csf('job_no')]]['main_item_category'];
				$short_fab_item_category=$s_fab_book_arr[$row[csf('job_no')]]['short_item_category'];
				$po_break_id=implode(",",array_unique(explode(",",$booking_dtls_arr[$row[csf('job_no')]]['po_id']))); 
				$fabric_color=implode(",",array_unique(explode(",",$booking_dtls_arr[$row[csf('job_no')]]['color_id'])));
				$division_name=implode(",",array_unique(explode(",",$division_id)));
				$total_pri_costing_dzn="";
				$price_quo_costing_per=$price_qou_costArray[$row[csf('quotation_id')]]['costing_per'];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				//echo $price_quo_costing_per;
				if($price_quo_costing_per==1) $total_pri_costing_dzn=$price_quotation_arr[$row[csf('quotation_id')]]['fab_knit_req'];
				else if($price_quo_costing_per==2) $total_pri_costing_dzn=$price_quotation_arr[$row[csf('quotation_id')]]['fab_knit_req']*12;
				else if($price_quo_costing_per==3) $total_pri_costing_dzn=$price_quotation_arr[$row[csf('quotation_id')]]['fab_knit_req']/2;
				else if($price_quo_costing_per==4) $total_pri_costing_dzn=$price_quotation_arr[$row[csf('quotation_id')]]['fab_knit_req']/3;
				else if($price_quo_costing_per==5) $total_pri_costing_dzn=$price_quotation_arr[$row[csf('quotation_id')]]['fab_knit_req']/4;
           
				$total_pre_costing_dzn="";
				$dzn_qnty=0;
				$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1)
				{
					$dzn_qnty=12*1;
					$total_pre_costing_dzn=$grey_qty_arr[$row[csf('job_no')]]['fab_knit'];
				}
				else if($costing_per_id==2)
				{
					$dzn_qnty=1;
					$total_pre_costing_dzn=$grey_qty_arr[$row[csf('job_no')]]['fab_knit']*12;
				}
				else if($costing_per_id==3)
				{
					$dzn_qnty=12*2;
					$total_pre_costing_dzn=$grey_qty_arr[$row[csf('job_no')]]['fab_knit']/2;
				}
				else if($costing_per_id==4)
				{
					$dzn_qnty=12*3;
					$total_pre_costing_dzn=$grey_qty_arr[$row[csf('job_no')]]['fab_knit']/3;
				}
				else if($costing_per_id==5)
				{
					$dzn_qnty=12*4;
					$total_pre_costing_dzn=$grey_qty_arr[$row[csf('job_no')]]['fab_knit']/4;
				}
				
				$main_booking_qty=0; $short_booking_qty=0; $fin_booking_qty=0; $row_grey_qty=0;
				$main_booking_qty=$booking_qty_arr[$row[csf('job_no')]][2]['grey'];	
				$short_booking_qty=$booking_qty_arr[$row[csf('job_no')]][1]['grey'];
				$fin_booking_qty=$booking_qty_arr[$row[csf('job_no')]][1]['fin'];
				$row_grey_qty=$main_booking_qty+$short_booking_qty;
				
				//$po_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$po_qty_pcs=array_sum($po_pcs_qty_arr[$row[csf('job_no')]]);
				$po_qty=array_sum($po_qty_arr[$row[csf('job_no')]]);
				
				
				//$total_booking_cons_dzn=($row_grey_qty/$po_qty_pcs)*12;
				$total_booking_cons_dzn=($row_grey_qty/$po_qty)*12;
				
				$total_variance_cons_dzn=($total_pre_costing_dzn-$total_booking_cons_dzn);
				$total_price_quo_variance_dzn=($total_booking_cons_dzn-$total_pri_costing_dzn);
				$excess_percent=$short_booking_qty/$main_booking_qty*100;
				//$dept_responsible=implode(",",$responsible_dept_arr[$row[csf('job_no')]]) ; 
				
				//$person_responsible=implode(",",$responsible_person_arr[$row[csf('job_no')]]); 
				$person_responsible=implode(",",array_unique(explode(',',$responsible_person_arr[$row[csf('job_no')]])));
				//echo $person_responsible.'dd';
				
				$reason_result=implode(",",array_unique(explode(",",$reason_arr[$row[csf('job_no')]])));
				$reason_data_arr=explode(",",$reason_result);
				$reason_name_data='';
				foreach($reason_data_arr as $reason_data)
				{
					if($reason_name_data=="") $reason_name_data=$reason_data; else $reason_name_data.=",".$reason_data;
				}
				$dept_responsible_name_data='';
				$dept_responsible_pre_name1=implode(",",array_unique(explode(",",$responsible_dept_arr[$row[csf('job_no')]])));
				$dept_responsible_pre_name=explode(",",$dept_responsible_pre_name1);
				//print_r($dept_responsible_pre_name);
				foreach($dept_responsible_pre_name as $dept_response)
				{
					//echo $dept_response;
					if($dept_responsible_name_data=="") $dept_responsible_name_data=$department_name_library[$dept_response]; else $dept_responsible_name_data.=",".$department_name_library[$dept_response];
				}
				//echo $dept_responsible_name_data.'ss';
				$fabric_color_data='';
				$fabric_color_id=explode(",",$fabric_color);
				
				foreach($fabric_color_id as $fabcolor_id)
				{
					if($fabric_color_data=="") $fabric_color_data=$color_library[$fabcolor_id]; else $fabric_color_data.=", ".$color_library[$fabcolor_id];
				}
				$order_number_arr=''; $po_id_all="";
				$order_number=explode(",",$po_break_id);
				foreach($order_number as $po_id)
				{	if($po_id>0)
					{
						if($order_number_arr=="") $order_number_arr=$booking_po_arr[$po_id]['po_no']; else $order_number_arr.=",".$booking_po_arr[$po_id]['po_no'];
						if($po_id_all=="") $po_id_all=$po_id; else $po_id_all.=",".$po_id;
					}
				}
				$booking_date=array_unique(explode(",",$date_string_arr[$row[csf('job_no')]]));
				$booking_date_data='';
				foreach($booking_date as $booking_date_res)
				{
					if($booking_date_data=="") $booking_date_data=change_date_format($booking_date_res); else $booking_date_data.=",".change_date_format($booking_date_res);
				}
				if($total_pre_costing_dzn<$total_booking_cons_dzn) $color_pre="red";
				else if($total_pre_costing_dzn>$total_booking_cons_dzn) $color_pre="green";	
				else $color_pre="";	
				
            	//print_r($short_booking_no_prefix);
				if($short_booking_no_prefix!="")
				{     
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="60" align="center"><p><? echo $row[csf('year')] ; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[csf('job_no_prefix_num')] ; ?></p></td>
                        <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></div></td>
                        
                        <td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('grouping')] ; ?></div></td>
                        <td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('file_no')] ; ?></div></td>
                        
                        <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $booking_date_data; ?></div></td>
                        <td width="100">
							<?
                            $main_booking_type=2;
                            $short_booking_type=1;
                            $short_booking_data_arr='';
                            //print_r($short_booking_no_large_data).'<br>';
                            foreach( $short_booking_data as $sb_row)
                            {
								if($sb_row>0)
								{	
									if($short_booking_data_arr=="") $short_booking_data_arr="<a href='##' onclick=\"ms_booking_no_popup('".$short_booking_type."','".$company_name."','".$short_booking_no_large_arr[$sb_row]."','".$po_id_all."','".$row[csf('job_no')]."','".$short_fab_source."','".$short_fab_item_category."','".$is_app_arr[$sb_row][$short_booking_type]."')\">".$sb_row."</a>"; 
									else $short_booking_data_arr.=","."<a href='##' onclick=\"ms_booking_no_popup('".$short_booking_type."','".$company_name."','". $short_booking_no_large_arr[$sb_row]."','".$po_id_all."','".$row[csf('job_no')]."','".$short_fab_source."','".$short_fab_item_category."','".$is_app_arr[$sb_row][$short_booking_type]."')\">".$sb_row."</a>";
								}
							}
            				$main_booking_data_arr='';
							//print_r($main_booking_data);
							foreach( $main_booking_data as $mb_row)
							{
								if($mb_row>0)
								{
									if($main_booking_data_arr=="")
									{ 
										$main_booking_data_arr="<a href='##' onclick=\"ms_booking_no_popup('".$main_booking_type."','".$company_name."','".$main_booking_no_large_arr[$mb_row]."','".$po_id_all."','".$row[csf('job_no')]."','".$main_fab_source."','".$main_fab_item_category."','".$is_app_arr[$mb_row][$main_booking_type]."')\">".$mb_row."</a>";
									}
									else 
									{
										$main_booking_data_arr.=","."<a href='##' onclick=\"ms_booking_no_popup('".$main_booking_type."','".$company_name."','".$main_booking_no_large_arr[$mb_row]."','".$po_id_all."','".$row[csf('job_no')]."','".$main_fab_source."','".$main_fab_item_category."','".$is_app_arr[$mb_row][$main_booking_type]."')\">".$mb_row."</a>";
									}
								}
							}
						?><div style="word-wrap:break-word; width:100px"><? echo "M: ".$main_booking_data_arr; echo "; S: ".$short_booking_data_arr; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_number_arr ; ?></div></td>
						<td width="100" ><div style="word-wrap:break-word; width:100px"><? echo $fabric_color_data; ?></div></td>
                        <td width="70" align="right"><p><? echo $po_qty_pcs; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($main_booking_qty,2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($short_booking_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($fin_booking_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row_grey_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_pre_costing_dzn,2) ; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_pri_costing_dzn,2) ; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_booking_cons_dzn,2); ?></p></td>
                        <td width="80" bgcolor="<? echo $color_pre;?>"  align="right"><p><? echo number_format($total_variance_cons_dzn,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_price_quo_variance_dzn,2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($excess_percent,2); ?></p></td>
						<td width="100" align="center"><p><? echo $division_name; ?></p></td>
                        <td width="90" title="From Short Booking"><div style="word-wrap:break-word; width:90px"><? echo $dept_responsible_name_data; ?></div></td>
                        <td width="80" title="From Short Booking"><div style="word-wrap:break-word; width:80px"><? echo ltrim($person_responsible,","); ?></div></td>
                        <td title="From Short Booking"><div style="word-wrap:break-word; width:72px"><? echo $reason_name_data; ?></div></td>
            		</tr>
					<?
                    $total_main_booking_qty+=$main_booking_qty;
                    $total_grey_qty_kg+=$row[csf('cpa_grey_qty')];
                    $total_fin_qty+=$fin_booking_qty;
                    $total_grey_fab_qnty+=$row_grey_qty;
					$total_order_qty_pcs+=$po_qty_pcs;
					$total_cpa_grey_qnty_kg+=$short_booking_qty;;
                    $i++;
                }
            }
            ?>
            </table>
            <table class="rpt_table" width="2110" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all"> 
                <tfoot>
                    <th width="30"></th>
                    <th width="60"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"> </th>
                    <th width="70" id="total_order_qty_pcs"><?  echo number_format($total_order_qty_pcs) ?></th>
                    <th width="80" id="value_main_booking_qty"><?  echo number_format($total_main_booking_qty,2) ?></th>
                    <th width="70" id="value_cpa_grey_qnty_kg"><?  echo number_format($total_cpa_grey_qnty_kg,2) ?></th>
                    <th width="80" id="value_total_fin_qty"><? echo number_format($total_fin_qty,2)?></th>
                    <th width="80" id="value_total_grey_fab_qnty"><? echo number_format($total_grey_fab_qnty,2) ?></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
					 <th width="70"></th>
					<th width="100"></th>
                    <th width="90"></th>
                    <th width="80"></th>
                    <th></th>
                </tfoot>
            </table>
            </div>
		</fieldset>
		</div>
		<?	
		}
		
		else if($rpt_type==2) //FSO
		{
			if($rpt_type==2 && ($search_date_type==1 || $search_date_type==3)) //Ship Date and Country Ship Date
			{
				$width="3110";//2670
				$row_span="30";
			}
			else if($rpt_type==2 && $search_date_type==2) //Booking Date
			{
				$width="2870";
				$row_span="33";
			}
			?>
		
		<div style="width:<? echo $width;?>px;" id="content_search_panel2">
		<fieldset style="width:100%;">	
            <table width="<? echo $width;?>">
                <tr class="form_caption">
                    <td colspan="<? echo $row_span;?>" align="center"><strong><? echo $report_title; if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo '; '.$search_by[str_replace("'","",$cbo_search_date)].' From : '.change_date_format(str_replace("'","",$txt_date_from)).' To : '.change_date_format(str_replace("'","",$txt_date_to)) ?></strong></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="<? echo $row_span;?>" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                </tr>
            </table>
            <table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Year</th>
                        <th width="100">Job No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style</th>
                        <th width="100">Team</th>
                        <th width="100">Merchandiser</th>
                        <th width="100">Booking Date</th>
                        <th width="70">Booking Type</th>
                        <th width="100">Booking No</th>
                        <th width="100">Supplier</th>
                        <th width="110">FSO No</th>
                        <th width="100">Fabric Color</th>
                        <th width="80">Order Qty(pcs)</th>
                        <th width="80">Main Booking Qty Finish(Kg)</th>
						<th width="80">Main Booking Qty Finish(Yds)</th>
						<th width="80">Main Booking Qty Finish(Mtr)</th>
						   
						
                        <th width="80">Main Booking Qty(Grey)</th>
                      
						<th width="80">Short Finish (Kg)</th>
						<th width="80">Short Finish (Yds)</th>
						<th width="80">Short Finish (Mtr)</th>
						
						
						
                        <th width="80">Short Grey (Qty)</th>
                        <th width="80">Total Grey Qty</th>
						
                        <th width="80">Total Finish Qty(Kg)</th>
						<th width="80">Total Finish Qty(Yds)</th>
						<th width="80">Total Finish Qty(Mtr)</th>
						<th width="80">Total Short Value(USD)</th>
                        <?
                        if($rpt_type==2 && ($search_date_type==1 || $search_date_type==3))
						{
						?>
                        <th width="80">BOM Cons/Dzn</th>
                        <th width="80">Total Booking Cons/Dzn</th>
                        <th width="80">BOM Variance</th>
                        <?
						}
						?>
                        <th width="60">Excess Fabric KG(%)</th>
						<th width="60">Excess Fabric Yds(%)</th>
						<th width="60">Excess Fabric Mtr(%)</th>
                        <th width="100">Division</th>
						<th width="100">Responsible Dept.</th>
                        <th width="100">Responsible Person</th>
                        <th>Reason</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<? echo $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?	
            $fabriccostArray=array();$date_string_arr=array(); $grey_qty_arr=array(); $responsible_dept_arr=array(); $responsible_person_arr=array(); $reason_arr=array();
            $booking_no_arr=array(); $short_booking_no_arr=array(); $booking_no_large_arr=array();$booking_qty_arr=array();
            $price_quotation_arr=array(); $price_qou_costArray=array(); $booking_dtls_arr=array(); $is_app_arr=array();
     
         	$s_fab_book_arr=array();
            $item_fab_book_arr=array();
		  
            $sm_fab_book_arr=array();
            $fab_sql=sql_select("select b.uom,b.id as pre_cost_dtls_id,c.is_short,d.job_no,c.fabric_source as main_fabric_source,c.item_category as main_item_category,d.division_id  from wo_po_details_master a, wo_booking_mst c,wo_booking_dtls d,wo_pre_cost_fabric_cost_dtls b where c.booking_no=d.booking_no   and d.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no and c.booking_type=1  and  a.job_no=d.job_no and c.company_id='$company_name' $job_no_cond and c.status_active=1 and c.is_deleted=0  ");
			
            foreach($fab_sql as $sm_data)
            {
				if($sm_data[csf('is_short')]==2)
				{
				$sm_fab_book_arr[$sm_data[csf('job_no')]]['main_fabric_source']=$sm_data[csf('main_fabric_source')];
				$sm_fab_book_arr[$sm_data[csf('job_no')]]['main_item_category']=$sm_data[csf('main_item_category')];
				}
				
				$uom_fab_book_arr[$sm_data[csf('job_no')]][$sm_data[csf('pre_cost_dtls_id')]]['uom']=$sm_data[csf('uom')];
				if($sm_data[csf('is_short')]==1)
				{
						$s_fab_book_arr[$sm_data[csf('job_no')]]['short_fabric_source']=$sm_data[csf('main_fabric_source')];
						$s_fab_book_arr[$sm_data[csf('job_no')]]['short_item_category']=$sm_data[csf('main_item_category')];
						if($sm_data[csf('division_id')]!=0)
						{
							$s_fab_book_arr[$sm_data[csf('job_no')]]['division_id'].=$short_division_array[$sm_data[csf('division_id')]].',';
						}
				}
            }  //var_dump($uom_fab_book_arr);
			
			unset($fab_sql);
			
			  $sql_b_date="select a.job_no,a.job_quantity,b.po_number, c.booking_date,c.entry_form, d.responsible_dept, d.responsible_person, d.reason, c.is_short,  
				c.booking_no_prefix_num, c.booking_no, c.is_approved, d.fabric_color_id,d.pre_cost_fabric_cost_dtls_id, d.po_break_down_id, sum(d.grey_fab_qnty) as grey_fab_qnty, sum(d.fin_fab_qnty) as fin_fab_qnty ,sum(b.po_quantity) as po_quantity,a.total_set_qnty
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d 
				where a.job_no=b.job_no_mst  and a.job_no=d.job_no and 
				b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type=1 and a.company_name='$company_name' and 
				a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and 
				c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $buyer_id_cond $job_no_cond  $year_cond  group by a.job_no,a.job_quantity,a.total_set_qnty,b.po_number, c.booking_date,c.entry_form, d.responsible_dept, d.responsible_person, d.reason, c.is_short,  
				c.booking_no_prefix_num, c.booking_no, c.is_approved, d.fabric_color_id, d.po_break_down_id,d.pre_cost_fabric_cost_dtls_id";
				$result_date=sql_select($sql_b_date);
					$responsible_person='';
            foreach($result_date as $row_date)
            {
				$date_string_arr[$row_date[csf('job_no')]].=$row_date[csf('booking_date')].',';	
				$responsible_dept_arr[$row_date[csf('job_no')]].=$row_date[csf('responsible_dept')].',';	
				if($row_date[csf('is_short')]==1)
				{
					/*if($row_date[csf('responsible_person')]!='')
					{
						if($responsible_person=='') $responsible_person=$row_date[csf('responsible_person')];else $responsible_person.=",".$row_date[csf('responsible_person')];
						$responsible_person_arr[$row_date[csf('job_no')]]=$responsible_person;	
						
				 	}
					if($row_date[csf('reason')]!='')
					{
						$reason_arr[$row_date[csf('job_no')]].=$row_date[csf('reason')].',';
					}*/
				}
				if(str_replace("'","",$row_date[csf('booking_no')])!="")
				{
					
					if($row_date[csf('entry_form')]==108 && $row_date[csf('is_short')]==2 )
					{
						$pb_booking_no_arr[$row_date[csf('job_no')]][$row_date[csf('entry_form')]].=$row_date[csf('booking_no_prefix_num')].',';
						$pb_booking_no_large_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no')].',';
					}
					else if($row_date[csf('entry_form')]!=108 && $row_date[csf('is_short')]==2 )
					{
						$booking_no_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no_prefix_num')].',';
					}
					else if($row_date[csf('entry_form')]!=108 && $row_date[csf('is_short')]==1 )
					{
						$booking_no_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no_prefix_num')].',';
					}
					$uom_id=$uom_fab_book_arr[$row_date[csf('job_no')]][$row_date[csf('pre_cost_fabric_cost_dtls_id')]]['uom'];
					//if($uom_id>0) echo $uom_id.',';
					//if($uom_id!=0)
					//{
					
					if($uom_id==12)
					{
						//$main_booking_grey_qty_kg+=$booking_qty_arr[$job_no][2][$uom_id]['grey'];
							$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['grey_kg']+=$row_date[csf('grey_fab_qnty')];
					}
					else if($uom_id==27)
					{
						$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['grey_yds']+=$row_date[csf('grey_fab_qnty')];
					}
					else if($uom_id==23)
					{
						
						//echo $row_date[csf('grey_fab_qnty')].', ';
						$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['grey_mtr']+=$row_date[csf('grey_fab_qnty')];
					}
							
					//$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]][$uom_id]['grey']+=$row_date[csf('grey_fab_qnty')];
					$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['fin']+=$row_date[csf('fin_fab_qnty')];
					//}
					$booking_po_arr[$row_date[csf('po_break_down_id')]]['po_no']=$row_date[csf('po_number')];
					$booking_dtls_arr[$row_date[csf('job_no')]]['po_id'].=$row_date[csf('po_break_down_id')].',';
					$booking_dtls_arr[$row_date[csf('job_no')]]['color_id'].=$row_date[csf('fabric_color_id')].',';
					$booking_no_large_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]].=$row_date[csf('booking_no')].',';
					$is_app_arr[$row_date[csf('booking_no_prefix_num')]][$row_date[csf('is_short')]]=$row_date[csf('is_approved')];
					
					$po_pcs_qty_arr[$row_date[csf('job_no')]][$row_date[csf('po_break_down_id')]]=$row_date[csf('po_quantity')]*$row_date[csf('total_set_qnty')];
					$po_qty_arr[$row_date[csf('job_no')]][$row_date[csf('po_break_down_id')]]=$row_date[csf('po_quantity')];
					
				}
            } 
         	// print_r($booking_qty_arr);die;
			unset($result_date);
			
            $i=1;
			if($db_type==0)
            {
				
				$sql="select  a.id as mst_id,a.job_no,a.job_quantity,a.style_ref_no,a.team_leader,a.dealing_marchant, YEAR(a.insert_date) as year, a.job_no_prefix_num, a.company_name, a.total_set_qnty as ratio, a.buyer_name, a.quotation_id,
					sum(distinct b.po_quantity) as po_quantity,sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.amount) as amount ,c.short_booking_type,c.booking_no_prefix_num,c.supplier_id,c.pay_mode,c.booking_no,c.booking_date,c.currency_id,c.exchange_rate,d.reason,d.division_id,d.pre_cost_fabric_cost_dtls_id,d.responsible_person,d.responsible_dept
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d
				where a.job_no=b.job_no_mst  and a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no  and c.booking_type=1 and c.is_short=1 and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $date_cond $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $ref_no_cond $file_no_cond $cbo_booking_type_cond  $division_cond  
				group by a.id,a.job_no,a.job_quantity,a.style_ref_no, a.job_no_prefix_num,a.team_leader,a.dealing_marchant, a.company_name, a.total_set_qnty, a.buyer_name, a.insert_date, a.quotation_id, a.job_quantity,c.short_booking_type,c.booking_no_prefix_num,c.exchange_rate,c.currency_id,c.supplier_id,c.pay_mode,c.booking_no,d.responsible_person,d.responsible_dept,d.reason,d.pre_cost_fabric_cost_dtls_id,d.division_id order by a.job_no_prefix_num DESC";
            }
            else if($db_type==2)
            {
				 $sql="select a.id as mst_id,a.job_no,a.job_quantity, a.style_ref_no,a.job_quantity,a.team_leader,a.dealing_marchant,to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num, a.company_name, a.total_set_qnty as ratio, a.buyer_name, a.quotation_id,
					sum(distinct b.po_quantity) as po_quantity,sum(d.grey_fab_qnty) as grey_fab_qnty,sum(d.fin_fab_qnty) as fin_fab_qnty,c.short_booking_type,c.booking_no_prefix_num,c.supplier_id,c.pay_mode,c.booking_no,c.exchange_rate,c.currency_id,c.booking_date,d.responsible_person,d.reason,d.responsible_dept,d.pre_cost_fabric_cost_dtls_id,d.division_id,sum(d.amount) as amount 
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d
				where a.job_no=b.job_no_mst and a.job_no=c.job_no  and a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no  and c.booking_type=1  and c.is_short=1  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $date_cond $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $ref_no_cond $file_no_cond $cbo_booking_type_cond $division_cond  $short_booking_type_cond
				group by a.id,a.job_no,a.job_quantity,a.style_ref_no, a.job_no_prefix_num, a.team_leader,a.dealing_marchant,a.company_name, a.total_set_qnty, a.buyer_name, a.insert_date, a.quotation_id, a.job_quantity,c.short_booking_type,c.booking_no_prefix_num,c.exchange_rate,c.supplier_id,c.currency_id,c.pay_mode,c.booking_no,c.booking_date,d.reason,d.responsible_dept,d.responsible_person,d.pre_cost_fabric_cost_dtls_id,d.division_id order by a.job_no_prefix_num DESC";//and c.is_short=1
            }
			//echo $sql;
			$result=sql_select($sql);$all_bo_id='';
            $tot_rows=count($result);$usd_id=2;
			foreach($result as $row )
            {
				$short_fabric_data_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
				$short_fabric_data_arr[$row[csf('job_no')]]['job']=$row[csf('job_no')];
				$short_fabric_data_arr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
				$short_fabric_data_arr[$row[csf('job_no')]]['prefix_job']=$row[csf('job_no_prefix_num')];
				$short_fabric_data_arr[$row[csf('job_no')]]['ratio']=$row[csf('ratio')];
				$short_fabric_data_arr[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('ratio')];
				$short_fabric_data_arr[$row[csf('job_no')]]['style']=$row[csf('style_ref_no')];
				$short_fabric_data_arr[$row[csf('job_no')]]['quotation_id']=$row[csf('quotation_id')];
				$short_fabric_data_arr[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
				$short_fabric_data_arr[$row[csf('job_no')]]['dealing']=$row[csf('dealing_marchant')];
				$short_fabric_data_arr[$row[csf('job_no')]]['short_booking_type']=$row[csf('short_booking_type')];
				$short_fabric_data_arr[$row[csf('job_no')]]['supplier_id']=$row[csf('supplier_id')];
				$short_fabric_data_arr[$row[csf('job_no')]]['pay_mode']=$row[csf('pay_mode')];
				$short_fabric_data_arr[$row[csf('job_no')]]['fabric_cost_dtls_id'].=$row[csf('pre_cost_fabric_cost_dtls_id')].',';
				$short_fabric_data_arr[$row[csf('job_no')]]['booking_no_prefix_num'].=$row[csf('booking_no_prefix_num')].',';
				$short_fabric_data_arr[$row[csf('job_no')]]['booking_date'].=change_date_format($row[csf('booking_date')]).',';
				
				if($row[csf('responsible_person')]!='')
				{
					$short_fabric_data_arr[$row[csf('job_no')]]['responsible_person'].=$row[csf('responsible_person')].',';
				}
				if($row[csf('reason')]!='')
				{
					$short_fabric_data_arr[$row[csf('job_no')]]['reason'].=$row[csf('reason')].',';
				}
				if($row[csf('responsible_dept')]!='')
				{
					$short_fabric_data_arr[$row[csf('job_no')]]['responsible_dept'].=$row[csf('responsible_dept')].',';
				}
				if($row[csf('division_id')]!='')
				{
					$short_fabric_data_arr[$row[csf('job_no')]]['division_id'].=$short_division_array[$row[csf('division_id')]].',';
				}
				//$responsible_dept_arr[$row_date[csf('job_no')]].=$row_date[csf('responsible_dept')].',';	
				
				//if($responsible_person=='') $responsible_person=$row_date[csf('responsible_person')];else $responsible_person.=",".$row_date[csf('responsible_person')];
						//$responsible_person_arr[$row_date[csf('job_no')]]=$responsible_person;	
						
				
				$short_fabric_data_arr[$row[csf('job_no')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$uom_id=$uom_fab_book_arr[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['uom'];
				if($db_type==0)
				{
					$conversion_date=change_date_format($row[csf('booking_date')], "Y-m-d", "-",1);
				}
				else
				{
					$conversion_date=change_date_format($row[csf('booking_date')], "d-M-y", "-",1);
				}
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						
				
				if($uom_id==12)
				{
					$short_fabric_data_arr[$row[csf('job_no')]]['grey_fab_qnty_kg']+=$row[csf('grey_fab_qnty')];
					if($row[csf('currency_id')]==1) //Tk
					{
						$short_fabric_data_arr[$row[csf('job_no')]]['amount_kg']+=$row[csf('amount')]/$currency_rate;
					}
					else
					{
						$short_fabric_data_arr[$row[csf('job_no')]]['amount_kg']+=$row[csf('amount')];
					}
				}
				else if($uom_id==27)
				{
					//echo $uom_id.'DD'.$row[csf('amount')];
					$short_fabric_data_arr[$row[csf('job_no')]]['grey_fab_qnty_yds']+=$row[csf('grey_fab_qnty')];
					if($row[csf('currency_id')]==1) //Tk
					{
						$short_fabric_data_arr[$row[csf('job_no')]]['amount_yds']+=$row[csf('amount')]/$currency_rate;
					}
					else
					{
						$short_fabric_data_arr[$row[csf('job_no')]]['amount_yds']+=$row[csf('amount')];
					}
				}
				else if($uom_id==23)
				{
					$short_fabric_data_arr[$row[csf('job_no')]]['grey_fab_qnty_mtr']+=$row[csf('grey_fab_qnty')];
					if($row[csf('currency_id')]==1) //Tk
					{
						$short_fabric_data_arr[$row[csf('job_no')]]['amount_mtr']+=$row[csf('amount')]/$currency_rate;
					}
					else
					{
						$short_fabric_data_arr[$row[csf('job_no')]]['amount_mtr']+=$row[csf('amount')];
					}
				}
				//$booking_qty_arr[$row_date[csf('job_no')]][$row_date[csf('is_short')]]['fin']+=$row_date[csf('fin_fab_qnty')];
				$short_fabric_data_arr[$row[csf('job_no')]]['job_quantity']=$row[csf('job_quantity')]*$row[csf('ratio')];
				$short_booking=$booking_no_arr[$row[csf('job_no')]][1];
				if($short_booking!='')
				{
					$short_fabric_data_arr[$row[csf('job_no')]]['booking_no'].=$row[csf('booking_no')].',';
					//echo $row[csf('booking_no')].'m';
				}
				if($all_bo_id=='') $all_bo_id=$row[csf('mst_id')];else $all_bo_id.=','.$row[csf('mst_id')];
			}
					$boIds=chop($all_bo_id,',');$bo_cond_for_in=""; //order_cond1=""; $order_cond2=""; $precost_po_cond="";
					$bo_ids=count(array_unique(explode(",",$all_bo_id)));
						if($db_type==2 && $bo_ids>1000)
						{
							$bo_cond_for_in=" and (";
							$boIdsArr=array_chunk(explode(",",$boIds),999);
							foreach($boIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$bo_cond_for_in.=" a.booking_id in($ids) or"; 
								
							}
							$bo_cond_for_in=chop($bo_cond_for_in,'or ');
							$bo_cond_for_in.=")";
							
						}
						else
						{
								$bo_ids=implode(',',array_unique(explode(",",$boIds)));
							$bo_cond_for_in=" and a.booking_id in($bo_ids)";
						}
						//echo $bo_cond_for_in;
						
			$sales_data=sql_select("select a.company_id, a.sales_booking_no,a.within_group,a.booking_id,a.job_no as fso_no,b.grey_qty,b.finish_qty,b.process_loss from  fabric_sales_order_mst a,fabric_sales_order_dtls b  where a.job_no=b.job_no_mst and  a.status_active=1 and a.is_deleted=0 and a.sales_booking_no is not null ");
			//echo "select a.company_id, a.sales_booking_no,a.within_group,a.booking_id,a.job_no as fso_no,b.grey_qty,b.finish_qty,b.process_loss from  fabric_sales_order_mst a,fabric_sales_order_dtls b  where a.job_no=b.job_no_mst and  a.status_active=1 and a.is_deleted=0 and a.sales_booking_no is not null $bo_cond_for_in";
			foreach($sales_data as $row)
			{
				$sales_no_arr[$row[csf('sales_booking_no')]]['fso_no']=$row[csf('fso_no')];
				$sales_no_arr[$row[csf('sales_booking_no')]]['booking_id']=$row[csf('booking_id')];
				$sales_no_arr[$row[csf('sales_booking_no')]]['within_group']=$row[csf('within_group')];
				$sales_no_qty_arr[$row[csf('fso_no')]]['grey_qty']+=$row[csf('grey_qty')];
				$sales_no_qty_arr[$row[csf('fso_no')]]['finish_qty']+=$row[csf('finish_qty')];
				$sales_no_qty_arr[$row[csf('fso_no')]]['company_id']=$row[csf('company_id')];
				$main_sales_no_qty_arr[$row[csf('sales_booking_no')]]['grey_qty']+=$row[csf('grey_qty')];
				//$sales_no_qty_arr[$row[csf('fso_no')]]['process_loss']+=$row[csf('process_loss')];
			}
			unset($sales_data);
			
			
			if($rpt_type==2 && ($search_date_type==1 || $search_date_type==3))
			{
				$condition= new condition();
				$condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				  if(str_replace("'","",$job_no!='')){
				  $condition->job_no("like '%".$job_no."%'");
				  }
				    if(str_replace("'","",$cbo_ref!='')){
				  $condition->grouping("like '%".$cbo_ref."%'");
				  }
				    if(str_replace("'","",$cbo_file!='')){
				  $condition->file_no("='".$cbo_file."");
				  }
				  if($search_date_type==1)
				  {
				  if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
					{
				  			$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
			 	  }
				   if($search_date_type==3)
				  {
				 	 if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
					{
				  			$condition->country_ship_date(" between '$start_date' and '$end_date'");
					}
			 	  }
				  $condition->init();
				$fabric= new fabric($condition);
				// echo $fabric->getQuery(); die;
				$req_qty_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish();
			}
				//print_r($req_qty_arr);
				
				
           $total_main_booking_fin_qty_kg=$total_main_booking_fin_qty_yds=$total_main_booking_grey_qty_mtr=$total_grey_fab_qnty=$total_fso_short_grey_qty=$total_tot_bom_cons=$total_tot_booking_cons_dzn=$total_fin_fab_qty_kg=$total_fin_fab_qty_yds=$total_fin_fab_qty_mtr=$total_short_grey_fab_qty=$total_short_grey_fab_qty_yds=$total_short_grey_fab_qty_yds=0;
            foreach($short_fabric_data_arr as $job_no=>$row )
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$short_booking_no_prefix_num=rtrim($row[('booking_no_prefix_num')],',');
				$division_id=rtrim($row[('division_id')],',');
				$main_booking_no_prefix=implode(",",array_unique(explode(',',$booking_no_arr[$job_no][2])));
				$partial_main_booking_no_prefix=implode(",",array_unique(explode(',',$pb_booking_no_arr[$job_no][108])));
				$short_booking_no_prefix=implode(",",array_unique(explode(',',$short_booking_no_prefix_num)));
				
				$main_booking_no_large=implode(",",array_unique(explode(',',$booking_no_large_arr[$job_no][2])));
				$short_booking_no_large=implode(",",array_unique(explode(',',$booking_no_large_arr[$job_no][1])));
				$pb_main_booking_no_large=implode(",",array_unique(explode(',',$pb_booking_no_large_arr[$job_no][2])));
				//print_r($main_booking_no_prefix);
				//echo $short_booking_no_large.'='.$main_booking_no_large.'<BR>';
				$main_booking_data=explode(',', $main_booking_no_prefix); 
				$short_booking_data=explode(",",$short_booking_no_prefix);
				$partial_main_booking_data=explode(',', $partial_main_booking_no_prefix); 
				
				$main_booking_large_data=explode(',', $main_booking_no_large); 
				$short_booking_large_data=explode(",",$short_booking_no_large);
				$pb_main_booking_large_data=explode(',', $pb_main_booking_no_large); 
				$main_booking_no_large_arr=array();
				foreach($main_booking_large_data as $mb_large)
				{	
					$ex_mb=explode('-',$mb_large);
					$main_booking_no_large_arr[ltrim($ex_mb[3],'0')]=$mb_large;
				}
				$pb_main_booking_no_large_arr=array();
				foreach($pb_main_booking_large_data as $pb_large)
				{	
					$ex_pb_mb=explode('-',$pb_large);
					$pb_main_booking_no_large_arr[ltrim($ex_pb_mb[3],'0')]=$pb_large;
				}
				
				$short_booking_no_large_arr=array();
				foreach($short_booking_large_data as $sb_large)
				{	//echo $sb_large.'<br>kkk';
					$ex_short=explode('-',$sb_large);
					$short_booking_no_large_arr[ltrim($ex_short[3],'0')]=$sb_large;
				}
				//print_r($short_booking_no_large_arr);
				$main_fab_source=$sm_fab_book_arr[$job_no]['main_fabric_source'];
				$short_fab_source=$s_fab_book_arr[$job_no]['short_fabric_source'];
				$division_id=rtrim($division_id,',');
				$main_fab_item_category=$sm_fab_book_arr[$job_no]['main_item_category'];
				$short_fab_item_category=$s_fab_book_arr[$job_no]['short_item_category'];
				$po_break_id=implode(",",array_unique(explode(",",$booking_dtls_arr[$job_no]['po_id']))); 
				$fabric_color=implode(",",array_unique(explode(",",$booking_dtls_arr[$job_no]['color_id'])));
				$division_name=implode(",",array_unique(explode(",",$division_id))); 
				$total_pri_costing_dzn="";
         		$pay_mode_id=$row[('pay_mode')];$supplier_id=$row[('supplier_id')];
				//echo $fso_no.'df';
				if($pay_mode_id==3 || $pay_mode_id==5)
				{
					$supplier_com=$company_library[$row[('supplier_id')]];	
				}
				else
				{
					$supplier_com=$supplier_name_arr[$row[('supplier_id')]];
				}
				//echo $row[('responsible_person')].'DD';
				$responsible_person=trim($row[('responsible_person')],',');
				$responsible_dept=trim($row[('responsible_dept')],',');
				$reasons=trim($row[('reason')],',');
				$responsible_person=$responsible_person;
				if($responsible_person!='')
				{
					$person_responsible=implode(",",array_unique(explode(',',$responsible_person)));
				}
				//echo $person_responsible.'dd';
				$reason_result=implode(",",array_unique(explode(",",$reasons)));
				$reason_data_arr=explode(",",$reason_result);
				$reason_name_data='';
				foreach($reason_data_arr as $reason_data)
				{
					if($reason_name_data=="") $reason_name_data=$reason_data; else $reason_name_data.=",".$reason_data;
				}
				$dept_responsible_name_data='';
				$dept_responsible_pre_name1=implode(",",array_unique(explode(",",$responsible_dept)));
				$dept_responsible_pre_name=explode(",",$dept_responsible_pre_name1);
				//print_r($dept_responsible_pre_name);
				foreach($dept_responsible_pre_name as $dept_response)
				{
					if($dept_responsible_name_data=="") $dept_responsible_name_data=$department_name_library[$dept_response]; else $dept_responsible_name_data.=",".$department_name_library[$dept_response];
				}
				$fabric_color_data='';$fabric_color_ids='';
				$fabric_color_id=explode(",",$fabric_color);
				foreach($fabric_color_id as $fabcolor_id)
				{
					if($fabcolor_id>0)
					{
					if($fabric_color_data=="") $fabric_color_data=$color_library[$fabcolor_id]; else $fabric_color_data.=", ".$color_library[$fabcolor_id];
					if($fabric_color_ids=='') $fabric_color_ids=$fabcolor_id; else $fabric_color_ids.=",".$fabcolor_id;
					}
				}
				if($fabric_color_ids!='') $view_txt="View";else $view_txt="";
				$color_button="<a href='##' onClick=\"generate_color_popup('".$fabric_color_ids."','".$company_name."','".$job_no."','".$row[('buyer')]."','".$row[('style')]."','show_fabric_color_dtls','1')\"> ".$view_txt." </a>";
				$order_number_arr=''; $po_id_all="";
				$order_number=explode(",",$po_break_id);
				foreach($order_number as $po_id)
				{	if($po_id>0)
					{
						if($order_number_arr=="") $order_number_arr=$booking_po_arr[$po_id]['po_no']; else $order_number_arr.=",".$booking_po_arr[$po_id]['po_no'];
						if($po_id_all=="") $po_id_all=$po_id; else $po_id_all.=",".$po_id;
					}
				}
				$booking_date=array_unique(explode(",",$date_string_arr[$job_no]));
				$booking_date_data='';
				foreach($booking_date as $booking_date_res)
				{
					if($booking_date_data=="") $booking_date_data=change_date_format($booking_date_res); else $booking_date_data.=",".change_date_format($booking_date_res);
				}
				if($total_pre_costing_dzn<$total_booking_cons_dzn) $color_pre="red";
				else if($total_pre_costing_dzn>$total_booking_cons_dzn) $color_pre="green";	
				else $color_pre="";	
            	//print_r($short_booking_no_prefix);
				if($short_booking_no_prefix!="")
				{ 
					$booking_no=rtrim($row['booking_no'],',');
					$booking_nos=array_unique(explode(",",$booking_no));
					$short_booking_date=rtrim($row[('booking_date')],',');
					$short_booking_date=implode(",",array_unique(explode(",",$short_booking_date)));
					
					//echo  $booking_no.'d';
					 $fso_nos='';
					$fso_short_grey_qty=$fso_short_fin_qty=0;//booking_id
					$sales_order='';
					foreach( $booking_nos as $sb_no)
					{
						$booking_id=$sales_no_arr[$sb_no]['booking_id'];
						$sales_nos=$sales_no_arr[$sb_no]['fso_no'];
						$fso_company_id=$sales_no_qty_arr[$sales_nos]['company_id'];
						//$short_grey_fab_qnty=$short_fabric_data_arr[$sb_no]['grey_fab_qnty'];
						//echo $sales_nos;
						if($fso_nos=='') $fso_nos=$sales_no_arr[$sb_no]['fso_no'];$fso_nos.=','.$sales_no_arr[$sb_no]['fso_no'];
						if($sales_nos!='')
						{
							if($sales_order=='') 
							{ 
								$sales_order = "<a href='##' style='color:#000' onclick=\"fnc_fabric_sales_order_print('" . $fso_company_id . "','" . $booking_id . "','" . $sb_no . "','" . $sales_nos . "','fabric_sales_order_print')\"><font style='font-weight:bold'>" . $sales_nos . "</a>";
							}
							else
							{
								$sales_order .=",". "<a href='##' style='color:#000' onclick=\"fnc_fabric_sales_order_print('" . $fso_company_id . "','" . $booking_id . "','" . $sb_no . "','" . $sales_nos . "','fabric_sales_order_print')\"><font style='font-weight:bold'>" . $sales_nos . "</a>";
							}
						}
					} 
					$short_fin_fab_qty=$row[('fin_fab_qnty')];
					$short_grey_fab_qty_kg=$row[('grey_fab_qnty_kg')];
					$short_grey_fab_qty_yds=$row[('grey_fab_qnty_yds')];
					$short_grey_fab_qty_mtr=$row[('grey_fab_qnty_mtr')];
					$short_grey_fab_amount=$row[('amount_kg')]+$row[('amount_yds')]+$row[('amount_mtr')];
					
					$fso_num=array_unique(explode(",",$fso_nos));
					foreach($fso_num as $fso)
					{
						$fso_short_grey_qty+=$sales_no_qty_arr[$fso]['grey_qty'];
						$fso_short_fin_qty+=$sales_no_qty_arr[$fso]['finish_qty'];   
					}
					//$uom_id=$uom_fab_book_arr[$job_no]['pre_cost_dtls_id'];
				
					$fabric_cost_dtls_id= rtrim($row[('fabric_cost_dtls_id')],'');
					$fabric_cost_dtls_ids= array_unique(explode(",",$row[('fabric_cost_dtls_id')]));
					//$main_booking_grey_qty_kg=$main_booking_grey_qty_yds=$main_booking_grey_qty_mtr=0;
					
					$main_booking_grey_qty_kg=$booking_qty_arr[$job_no][2]['grey_kg'];
					$main_booking_grey_qty_yds=$booking_qty_arr[$job_no][2]['grey_yds'];
					$main_booking_grey_qty_mtr=$booking_qty_arr[$job_no][2]['grey_mtr'];
					//echo $main_booking_grey_qty_mtr.'=';
					//$main_booking_grey_qty=$booking_qty_arr[$job_no][2]['grey'];
					//$main_booking_fin_qty=$booking_qty_arr[$job_no][2]['fin'];	
					//$short_booking_qty=$booking_qty_arr[$job_no][1]['grey'];
				
					$knit_pre_req_qty=array_sum($req_qty_arr['knit']['grey'][$job_no]);
					$wv_pre_req_qty=array_sum($req_qty_arr['woven']['grey'][$job_no]);
					$tot_bom_cons=(($knit_pre_req_qty+$wv_pre_req_qty)/$row['job_quantity'])*12;
					
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="60" align="center"><p><? echo $row[('year')] ; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[('prefix_job')] ; ?></p></td>
                        <td width="100"><p><? echo $buyer_short_name_library[$row[('buyer')]] ; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[('style')] ; ?></p></td>
                        <td width="100" align="center"><p><? echo $team_leader_arr[$row[('team_leader')]]; ?></p></td>
                        <td width="100"><p><? echo $deal_merchant_arr[$row[('dealing')]];//$booking_date_data; ?></p></td>
                        <td width="100">
							<?
                            $main_booking_type=2;
                            $short_booking_typ=1;
                            $short_booking_data_arr='';
                            //print_r($short_booking_no_large_data).'<br>';
                            foreach( $short_booking_data as $sb_row)
                            {
								if($sb_row>0)
								{	
									if($short_booking_data_arr=="") $short_booking_data_arr="<a href='##' onclick=\"ms_booking_no_popup('".$short_booking_typ."','".$company_name."','".$short_booking_no_large_arr[$sb_row]."','".$po_id_all."','".$job_no."','".$short_fab_source."','".$short_fab_item_category."','".$is_app_arr[$sb_row][$short_booking_typ]."')\">".$sb_row."</a>"; 
									else $short_booking_data_arr.=","."<a href='##' onclick=\"ms_booking_no_popup('".$short_booking_typ."','".$company_name."','". $short_booking_no_large_arr[$sb_row]."','".$po_id_all."','".$job_no."','".$short_fab_source."','".$short_fab_item_category."','".$is_app_arr[$sb_row][$short_booking_typ]."')\">".$sb_row."</a>";
									
								}
							}
            				$main_booking_data_arr='';$main_fso_short_grey_qty=0;
							//print_r($main_booking_data);
							foreach( $main_booking_data as $mb_row)
							{
								if($mb_row>0)
								{
									if($main_booking_data_arr=="")
									{ 
										$main_booking_data_arr="<a href='##' onclick=\"ms_booking_no_popup('".$main_booking_type."','".$company_name."','".$main_booking_no_large_arr[$mb_row]."','".$po_id_all."','".$job_no."','".$main_fab_source."','".$main_fab_item_category."','".$is_app_arr[$mb_row][$main_booking_type]."')\">".$mb_row."</a>";
									}
									else 
									{
										$main_booking_data_arr.=","."<a href='##' onclick=\"ms_booking_no_popup('".$main_booking_type."','".$company_name."','".$main_booking_no_large_arr[$mb_row]."','".$po_id_all."','".$job_no."','".$main_fab_source."','".$main_fab_item_category."','".$is_app_arr[$mb_row][$main_booking_type]."')\">".$mb_row."</a>";
									}
									//echo $main_sales_no_qty_arr[$mb_row]['grey_qty'].'D'.$mb_row;
									$main_fso_short_grey_qty+=$main_sales_no_qty_arr[$main_booking_no_large_arr[$mb_row]]['grey_qty'];
									//$main_sales_no_qty_arr[$row[csf('sales_booking_no')]]['grey_qty']
								}
							}
							//echo $main_fso_short_grey_qty.'FG';
							$pb_main_booking_data_arr='';
							foreach( $partial_main_booking_data as $pb_row)
							{
								if($pb_row>0)
								{
									if($pb_main_booking_data_arr=="")
									{ 
										$pb_main_booking_data_arr="<a href='##' onclick=\"ms_booking_no_popup('108','".$company_name."','".$pb_main_booking_no_large_arr[$pb_row]."','".$po_id_all."','".$job_no."','".$main_fab_source."','".$main_fab_item_category."','".$is_app_arr[$pb_row][$main_booking_type]."')\">".$pb_row."</a>";
									}
									else 
									{
										$pb_main_booking_data_arr.=","."<a href='##' onclick=\"ms_booking_no_popup('108','".$company_name."','".$main_booking_no_large_arr[$pb_row]."','".$po_id_all."','".$job_no."','".$main_fab_source."','".$main_fab_item_category."','".$is_app_arr[$pb_row][$main_booking_type]."')\">".$pb_row."</a>";
									}
									$main_fso_short_grey_qty+=$main_sales_no_qty_arr[$pb_main_booking_no_large_arr[$pb_row]]['grey_qty'];
								}
							}
							$tot_fin_qty_kg=$main_booking_grey_qty_kg+$short_grey_fab_qty_kg;//$main_booking_fin_qty+$short_fin_fab_qty;
							$tot_fin_qty_yds=$main_booking_grey_qty_yds+$short_grey_fab_qty_yds;
							$tot_fin_qty_mtr=$main_booking_grey_qty_mtr+$short_grey_fab_qty_mtr;
							$tot_grey_qty=$main_fso_short_grey_qty+$fso_short_grey_qty;//$main_booking_grey_qty+$fso_short_grey_qty;
							$tot_booking_cons_dzn=($tot_fin_qty/$row['job_quantity'])*12;
							$tot_excess_fab_per_kg=($short_grey_fab_qty_kg/$main_booking_grey_qty_kg)*100;
							$tot_excess_fab_per_yds=($short_grey_fab_qty_yds/$main_booking_grey_qty_yds)*100; 
							$tot_excess_fab_per_mtr=($short_grey_fab_qty_mtr/$main_booking_grey_qty_mtr)*100; 
							//echo $main_fso_short_grey_qty.'D';

							//partial_main_booking_data
						?><p><? echo $short_booking_date; ?></p></td>
						<td width="70"><p>&nbsp;<? echo $short_booking_type[$row['short_booking_type']]; ?></p></td>
						<td width="100" ><p><? echo  "M: ".$main_booking_data_arr; ?></p><p> <? echo "PB: ".$pb_main_booking_data_arr; ?><p> <? echo "S: ".$short_booking_data_arr; ?></p></td>
                        <td width="100" align="center"><p><? echo $supplier_com; ?></p></td>
                        <td width="110" align="center"><p><? echo $sales_order;//implode(",",array_unique(explode(",",$fso_nos))); //number_format($main_booking_qty,2); ?></p></td>
                        <td width="100" align="center"><p><? echo $color_button;//$fabric_color_data;?></p></td>
                        <td width="80" align="right" title="Job Qty"><p><? echo number_format($row['job_quantity'],0); ?></p></td>
						
						<td width="80" align="right" title="From Booking"><p><? echo number_format($main_booking_grey_qty_kg,2);//$main_booking_fin_qty ?></p></td>
						<td width="80" align="right" title="From Booking"><p><? echo number_format($main_booking_grey_qty_yds,2);//$main_booking_fin_qty ?></p></td>
						<td width="80" align="right" title="From Booking"><p><? echo number_format($main_booking_grey_qty_mtr,2);//$main_booking_fin_qty ?></p></td>
							  
                        <td width="80" align="right" title="By Main Booking No of Sales"><p><? echo number_format($main_fso_short_grey_qty,2) ;//$fso_short_grey_qty ?></p></td>
                      
                        <td width="80" align="right"  title="From  Short Booking Grey "><p><? echo number_format($short_grey_fab_qty_kg,2); ?></p></td>
						 <td width="80" align="right"  title="From  Short Booking Grey "><p><? echo number_format($short_grey_fab_qty_yds,2); ?></p></td>
						 <td width="80" align="right"  title="From  Short Booking Grey "><p><? echo number_format($short_grey_fab_qty_mtr,2); ?></p></td>
						
						  
                        <td width="80" align="right" title="By Short Booking Grey of Sales"><p><? echo number_format($fso_short_grey_qty,2) ;//$short_grey_fab_qty ?></p></td>
                        <td width="80" bgcolor="<? //echo $color_pre;?>"  title="Main booking Sales Grey qty + Short Grey qty" align="right"><p><? echo number_format($tot_grey_qty,2); ?></p></td>
                        <td width="80" bgcolor="<? //echo $color_pre;?>"  title="Main booking fin qty Kg+ Short Booking fin qty Kg " align="right"><p><? echo number_format($tot_fin_qty_kg,2); ?></p></td>
						 <td width="80" bgcolor="<? //echo $color_pre;?>"  title="Main booking fin qty Yds + Short Booking fin qty Yds" align="right"><p><? echo number_format($tot_fin_qty_yds,2); ?></p></td>
						  <td width="80" bgcolor="<? //echo $color_pre;?>"  title="Main booking fin qty Mtr+ Short Booking fin qty Mtr" align="right"><p><? echo number_format($tot_fin_qty_mtr,2); ?></p></td>
                         <?
                        if($rpt_type==2 && ($search_date_type==1 || $search_date_type==3))
						{
						?>
                        <td width="80" align="right"><p><? echo number_format($tot_bom_cons,2); ?></p></td>
                        <td width="80" align="right" title="Tot Fin Qty/Order Qty Pcs*12"><p><? echo number_format($tot_booking_cons_dzn,2); ?></p></td>
                        <td width="80" title="Tot BOM Cons-Tot Booking Cons"  align="right"><p><? echo number_format($tot_bom_cons-$tot_booking_cons_dzn,2); ?></p></td>
                        <?
						}
						?>
                        <td width="80" align="right"  title="From  Short Booking "><p><? echo number_format($short_grey_fab_amount,2); ?></p></td>
                        <td width="60" title="Short Fin QtyKg/Main Booking Fin QtyKg*100"  align="right"><p><? echo number_format($tot_excess_fab_per_kg,2);  ?></p></td>
						<td width="60" title="Short Fin QtyYds/Main Booking Fin QtyYds*100"  align="right"><p><? echo number_format($tot_excess_fab_per_yds,2);  ?></p></td>
						<td width="60" title="Short Fin QtyMtr/Main Booking Fin QtyMtr*100"  align="right"><p><? echo number_format($tot_excess_fab_per_mtr,2);  ?></p></td>
						  
						<td width="100" align="center"  title=""><p><? echo $division_name;?></p></td>
                        <td width="100" align="right"  title=""><p><? echo $dept_responsible_name_data;?></p></td>
                        <td width="100" align="right"><p><? echo  $person_responsible; ?></p></td>
                        <td width="" title="From Short Booking"><p><? echo $reason_name_data; ?></p></td>
            		</tr>
					<?
                    $total_main_booking_fin_qty_kg+=$main_booking_grey_qty_kg;
					$total_main_booking_fin_qty_yds+=$main_booking_grey_qty_yds;
					$total_main_booking_fin_qty_mtr+=$main_booking_grey_qty_mtr;
                    $total_main_booking_grey_qty+=$main_fso_short_grey_qty;
					$total_short_grey_fab_qty+=$short_grey_fab_qty_kg;
					$total_short_grey_fab_qty_yds+=$short_grey_fab_qty_yds;
					$total_short_grey_fab_qty_mtr+=$short_grey_fab_qty_mtr;
					$total_short_grey_fab_amount+=$short_grey_fab_amount;
                    $total_fin_qty+=$fin_booking_qty;
                    $total_grey_fab_qnty+=$tot_grey_qty;
					$total_fso_short_grey_qty+=$fso_short_grey_qty;
					$total_fso_short_fin_qty+=$fso_short_fin_qty;
					$total_tot_bom_cons+=$tot_bom_cons;
					$total_tot_booking_cons_dzn+=$tot_booking_cons_dzn;
					$total_bom_variance+=$tot_bom_cons-$tot_booking_cons_dzn;
					$total_order_qty_pcs+=$row['po_quantity'];
					$total_cpa_grey_qnty_kg+=$short_booking_qty;
					$total_fin_fab_qty_kg+=$tot_fin_qty_kg;
					$total_fin_fab_qty_yds+=$tot_fin_qty_yds;
					$total_fin_fab_qty_mtr+=$tot_fin_qty_mtr;
                    $i++;
                }
            }
            ?>
            </table>
            <table class="rpt_table" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all"> 
                <tfoot>
                <th width="30"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"> </th>
                <th width="70"> </th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="110"></th>
                <th width="100"></th>
                <th width="80" id="value_order_qty_pcs"><?  echo number_format($total_order_qty_pcs); ?></th>
				<th width="80" id="value_main_fin_book_qty"><?  echo number_format($total_main_booking_fin_qty_kg); ?></th>
				<th width="80" id="value_main_fin_book_qty_yds"><?  echo number_format($total_main_booking_fin_qty_yds); ?></th>
				<th width="80" id="value_main_fin_book_qty_mtr"><?  echo number_format($total_main_booking_fin_qty_mtr); ?></th>
					
                <th width="80" id="value_main_grey_book_qty"><?  echo number_format($total_main_booking_grey_qty); ?></th>
               
                <th width="80" id="value_short_fin_book_qty"><?  echo number_format($total_short_grey_fab_qty); ?></th>
				 <th width="80" id="value_short_fin_book_qty_yds"><?  echo number_format($total_short_grey_fab_qty_yds); ?></th>
				 <th width="80" id="value_short_fin_book_qty_mtr"><? echo number_format($total_short_grey_fab_qty_mtr); ?></th>
				 
                 <th width="80" id="value_short_grey_book_qty"><?  echo number_format($total_fso_short_grey_qty); ?></th> 
                <th width="80" id="value_tot_grey_book_qty"><?  echo number_format($total_grey_fab_qnty); ?></th>
                 <th width="80" id="value_tot_fin_book_qty"><?  echo number_format($total_fin_fab_qty_kg); ?></th>
				 <th width="80" id="value_tot_fin_book_qty_yds"><?  echo number_format($total_fin_fab_qty_yds); ?></th>
				 <th width="80" id="value_tot_fin_book_qty_mtr"><? echo number_format($total_fin_fab_qty_mtr); ?></th>
                  <?
				if($rpt_type==2 && ($search_date_type==1 || $search_date_type==3))
				{
					?>
                <th width="80" id="value_tot_bom_cons_qty"><?  echo number_format($total_tot_bom_cons) ?></th>
                <th width="80" id="value_total_booking_cons_dzn"><?  echo number_format($total_tot_booking_cons_dzn); ?></th>
                <th width="80" id="value_total_bom_variance"><?  echo number_format($total_bom_variance); ?></th>
                <?
				  }
				?>
				 <th width="80" id="value_short_fin_book_amount_usd"><? echo number_format($total_short_grey_fab_amount); ?></th>
                <th width="60"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th></th>
                </tfoot>
            </table>
            </div>
		</fieldset>
		</div>
	
		<? 
		}
		
	}
	
	echo "$total_data****$filename****$rpt_type****$search_date_type";
	exit(); 
}


if($action=="show_fabric_color_dtls")
	{
		echo load_html_head_contents("Fabric Color Popup","../../../../", 1, 1);
		extract($_REQUEST);
		//echo $color_id."sdsd";die;
			$sales_data=sql_select("select a.sales_booking_no,a.within_group,a.booking_id,a.job_no as fso_no,b.color_id,b.grey_qty,b.finish_qty,b.process_loss from  fabric_sales_order_mst a,fabric_sales_order_dtls b  where a.job_no=b.job_no_mst and  a.status_active=1 and a.is_deleted=0 and a.sales_booking_no is not null");
			foreach($sales_data as $row)
			{
				$sales_no_arr[$row[csf('sales_booking_no')]]['fso_no']=$row[csf('fso_no')];
				$sales_no_arr[$row[csf('sales_booking_no')]]['booking_id']=$row[csf('booking_id')];
				$sales_no_arr[$row[csf('sales_booking_no')]]['within_group']=$row[csf('within_group')];
				$sales_no_qty_arr[$row[csf('fso_no')]]['grey_qty']+=$row[csf('grey_qty')];
				$sales_no_qty_arr[$row[csf('fso_no')]]['finish_qty']+=$row[csf('finish_qty')];
				$main_sales_no_qty_arr[$row[csf('sales_booking_no')]][$row[csf('color_id')]]['grey_qty']+=$row[csf('grey_qty')];
			}
			unset($sales_data);
			
		  $sql_color="select a.job_no,c.entry_form,c.is_short,c.booking_no, d.fabric_color_id, d.po_break_down_id,
		  	(case when c.is_short=1 then d.grey_fab_qnty else 0 end) as short_grey_fab_qnty,
			(case when c.is_short=1 then d.fin_fab_qnty else 0 end) as  short_fin_fab_qnty,
			(case when c.is_short=2 then d.grey_fab_qnty else 0 end) as main_grey_fab_qnty,
			(case when c.is_short=2 then d.fin_fab_qnty else 0 end) as  main_fin_fab_qnty
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d 
				where a.job_no=b.job_no_mst  and a.job_no=d.job_no and 
				b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type=1 and a.company_name=$company_id and 
				a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and 
				c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.job_no='$job_no'  ";
				$result_color=sql_select($sql_color);
					
            foreach($result_color as $row)
            {
				$is_short=$row[csf('is_short')];
				$color_data_arr[$row[csf('fabric_color_id')]]['short_grey_fab_qnty']+=$row[csf('short_grey_fab_qnty')];
				$color_data_arr[$row[csf('fabric_color_id')]]['short_fin_fab_qnty']+=$row[csf('short_fin_fab_qnty')];
				$color_data_arr[$row[csf('fabric_color_id')]]['main_grey_fab_qnty']+=$row[csf('main_grey_fab_qnty')];
				$color_data_arr[$row[csf('fabric_color_id')]]['main_fin_fab_qnty']+=$row[csf('main_fin_fab_qnty')];
				if($is_short==2)
				{
					$color_data_arr[$row[csf('fabric_color_id')]]['booking_no'].=$row[csf('booking_no')].',';
				}
				else
				{
					$color_data_arr[$row[csf('fabric_color_id')]]['short_booking_no'].=$row[csf('booking_no')].',';
				}
				$color_data_arr[$row[csf('fabric_color_id')]]['job_no'].=$row[csf('job_no')].',';
				
			}
		?>
        <script>

        function print_window()
        {
           $("#table_body tr:first").hide();
		    var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
		$("#table_body tr:first").show();
            d.close();
        }
	
    </script>	
        <fieldset style="width:780px; margin-left:7px">
        <div id="report_container" align="center">
        	<div><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
            <table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="8">Color Wise Detail</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="140">Color</th>
                        <th width="100"> Main Booking Qty(Finish)</th>
                        <th width="100">Main Booking Qty(Grey)</th>
                        <th width="100">Short Grey (Qty)</th>
                        <th width="100">Short Finish (Qty)</th>
                        <th width="100"> Total Grey Qty</th>
                        <th>Total Finish Qty</th>
                    </tr>
                </thead>
                 <tbody  id="table_body">
                <?
               	 $i = 1;
                	$total_main_grey_fab_qnty =$total_main_sales_grey_fab_qnty=$total_short_sales_grey_fab_qnty=$total_tot_grey_qty=$total_tot_fin_qty=$total_short_grey_fab_qnty=0;
                	foreach($color_data_arr as $color=>$row)
					{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$main_booking_no=rtrim($row[('booking_no')],',');
							$short_booking_no=rtrim($row[('short_booking_no')],',');
							$main_booking_nos=array_unique(explode(",",$main_booking_no));
							$short_booking_nos=array_unique(explode(",",$short_booking_no));
							$main_sales_grey_qty=0;
							foreach($main_booking_nos as $mb_no)
							{
								$main_sales_grey_qty+=$main_sales_no_qty_arr[$mb_no][$color]['grey_qty'];
							}
							$short_sales_grey_qty=0;
							foreach($short_booking_nos as $sb_no)
							{
								$short_sales_grey_qty+=$main_sales_no_qty_arr[$sb_no][$color]['grey_qty'];
							}
							$tot_grey_qty=$main_sales_grey_qty+$short_sales_grey_qty;
							$tot_fin_qty=$row[('main_grey_fab_qnty')]+$row[('short_grey_fab_qnty')];
                    ?>
                   
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="140"><p><? echo $color_library[$color]; ?></p></td>
                        <td width="100" align="right"><? echo number_format($row[('main_grey_fab_qnty')],2); ?></td>
                        <td width="100" align="right"><p><? echo number_format($main_sales_grey_qty,2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($short_sales_grey_qty,2); ?></p></td>
                        <td width="100" align="right"><? echo number_format($row[('short_grey_fab_qnty')],2); ?> </td> 
                        <td width="100" align="right"><? echo number_format($tot_grey_qty,2); ?> </td> 
                        <td align="right"><? echo number_format($tot_fin_qty,2); ?> </td>
                    </tr>
                  
                    <?
                    $total_main_grey_fab_qnty+= $row[('main_grey_fab_qnty')];
					$total_main_sales_grey_fab_qnty += $main_sales_grey_qty;
					$total_short_sales_grey_fab_qnty += $short_sales_grey_qty;
					$total_short_grey_fab_qnty+= $row[('short_grey_fab_qnty')];
					$total_tot_grey_qty+= $tot_grey_qty;
					$total_tot_fin_qty+= $tot_fin_qty;
                    $i++;
                }
                ?>
                  </tbody>
                <tr style="font-weight:bold">
                <tfoot>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_main_grey_fab_qnty, 2); ?></th>
                    <th align="right"><? echo number_format($total_main_sales_grey_fab_qnty, 2); ?></th>
                    <th align="right"><? echo number_format($total_short_sales_grey_fab_qnty, 2); ?></th>
                    <th align="right"><? echo number_format($total_short_grey_fab_qnty, 2); ?></th>
                    <th align="right"><? echo number_format($total_tot_grey_qty, 2); ?></th>
                    <th align="right"><? echo number_format($total_tot_fin_qty, 2); ?></th>
                    </tfoot>
                </tr>
            </table>
          <script>
            setFilterGrid("table_body",-1 );
    	</script>		
        </div>
    </fieldset>
		<?
		exit();
	}
?>