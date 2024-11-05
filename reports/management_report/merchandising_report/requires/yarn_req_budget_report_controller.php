<?
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_id = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="pono_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:780px;">
                <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <th width="150">Buyer</th>
                        <th width="110">Search By</th>
                        <th width="130" id="search_by_td_up">Please Enter Order No</th>
                        <th width="130" colspan="2">Shipment Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                        <input type="hidden" name="hide_order_id" id="hide_order_id" value="" /></th> 
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 ); ?></td>                 
                            <td>	
                                <?
                                    $search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
                                    $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                ?>
                            </td>     
                            <td id="search_by_td"><input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date"></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px" placeholder="To Date"></td>	
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'yarn_req_budget_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) $search_field="b.po_number"; 
	else if($search_by==2) $search_field="a.style_ref_no"; 	
	else $search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else $date_cond="";
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year"; else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id DESC";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="yarn_popup")
{
  	echo load_html_head_contents("Yarn Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();
		
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_id').val(id);
			$('#hidden_name').val(name);
		}
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:370px; margin-left:10px">
            <input type="hidden" name="hidden_id" id="hidden_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_name" id="hidden_name" class="text_boxes" value="">
            <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            	<? if($type==1) $tdCaption="Yarn Composition"; else if($type==2) $tdCaption="Yarn Type"; else if($type==3) $tdCaption="Yarn Count"; ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                    <thead>
                        <th width="50">SL</th>
                        <th><? echo $tdCaption; ?></th>
                    </thead>
                </table>
                <div style="width:350px; overflow-y:scroll; max-height:300px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                    <?
                        $i=1;
						if($type==1)
						{
							foreach($composition as $id=>$name)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
									<td width="50" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									</td>	
									<td style="word-break:break-all"><? echo $name; ?></td>
								</tr>
								<?
								$i++;
							}
						}
						if($type==2)
						{
							foreach($yarn_type as $id=>$name)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
									<td width="50" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									</td>	
									<td style="word-break:break-all"><? echo $name; ?></td>
								</tr>
								<?
								$i++;
							}
						}
						if($type==3)
						{
							$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
							foreach($yarn_count_arr as $id=>$name)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
									<td width="50" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									</td>	
									<td style="word-break:break-all"><? echo $name; ?></td>
								</tr>
								<?
								$i++;
							}
						}
                    ?>
                    </table>
                </div>
                 <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%"> 
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>    
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');

	$company_name=str_replace("'","",$cbo_company_name);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	else $year_cond="";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no_cond=" and a.job_no_prefix_num in($txt_job_no)"; else $job_no_cond="";
	
	$txt_int_ref=str_replace("'","",$txt_int_ref);
	if(trim($txt_int_ref)!="") $int_ref_cond=" and b.grouping='$txt_int_ref'"; else $int_ref_cond="";
	
	$po_order_num=explode(",",str_replace("'","",$txt_order_no));
	$po_num_cond='';
	foreach($po_order_num as $po_num)
	{
		if($po_num_cond=='') $po_num_cond="'".$po_num."'";else $po_num_cond.=','."'".$po_num."'";
	}
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="") $po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		else $po_id_cond=" and b.po_number in ($po_num_cond)";
	}
	
	$hide_yarncomp_id=str_replace("'","",$hide_yarncomp_id);
	if(trim($hide_yarncomp_id)!="") $yarncomp_cond=" and d.copm_one_id in ($hide_yarncomp_id)"; else $yarncomp_cond="";
	
	$hide_yarntype_id=str_replace("'","",$hide_yarntype_id);
	if(trim($hide_yarntype_id)!="") $yarntype_cond=" and d.type_id in ($hide_yarntype_id) "; else $yarntype_cond="";
	
	$hide_yarncount_id=str_replace("'","",$hide_yarncount_id);
	if(trim($hide_yarncount_id)!="") $yarncount_cond=" and d.count_id in ( $hide_yarncount_id)"; else $yarncount_cond="";
	
	$cbo_bom_status=str_replace("'","",$cbo_bom_status);
	if($cbo_bom_status==1) $bom_status_cond="and e.approved=1"; else if($cbo_bom_status==2) $bom_status_cond="and e.approved!=1"; else $bom_status_cond="";
	
	//echo $job_no;
	
	$cbo_date_type=str_replace("'","",$cbo_date_type); $date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($cbo_date_type==1) $date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
		else if($cbo_date_type==2) $date_cond=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
		
	if($db_type==0) $year_field="YEAR(a.insert_date)"; else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')"; else $year_field="";//defined Later
			
	$sql="select a.job_no, a.buyer_name, $year_field as year, e.approved, a.job_no_prefix_num, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.grouping, d.copm_one_id, d.type_id, d.count_id
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fabric_cost_dtls c, wo_pre_cost_fab_yarn_cost_dtls d, wo_pre_cost_mst e, wo_po_color_size_breakdown f
	where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and a.job_no=e.job_no and b.job_no_mst=c.job_no and b.job_no_mst=d.job_no and b.job_no_mst=e.job_no and c.job_no=d.job_no and c.job_no=e.job_no and d.job_no=e.job_no and a.job_no=f.job_no_mst and c.job_no=f.job_no_mst and d.job_no=f.job_no_mst and e.job_no=f.job_no_mst
	and c.id=d.fabric_cost_dtls_id and b.id=f.po_break_down_id and a.company_name='$company_name' and d.rate>0
	$buyer_id_cond $year_cond $job_no_cond $int_ref_cond $po_id_cond $yarncomp_cond $yarntype_cond $yarncount_cond $date_cond $bom_status_cond
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by b.pub_shipment_date ASC";

	//echo $sql; //die;
	$result=sql_select($sql); $yarnCount_arr=array(); $dtlsData_arr=array(); $dtlsRow_arr=array(); $summaryBtnMonth_arr=array();
	foreach($result as $row)
	{
		$yarnCount_arr[$row[csf('count_id')]]=$row[csf('count_id')];
		$comp_type='';
		$comp_type=$row[csf('copm_one_id')].'__'.$row[csf('type_id')];
		
		$dtlsData_arr[strtotime(date("Y-m",strtotime($row[csf("pub_shipment_date")])))][$row[csf('job_no')]][$row[csf('po_id')]][$comp_type]['str']=$row[csf('buyer_name')].'**'.$row[csf('year')].'**'.$row[csf('approved')].'**'.$row[csf('job_no_prefix_num')].'**'.$row[csf('po_number')].'**'.$row[csf('pub_shipment_date')].'**'.$row[csf('po_received_date')].'**'.$row[csf('grouping')];
		$summaryBtnMonth_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
			//$summaryBtnMonth_arr[strtotime(date("Y-m",strtotime($row[csf("pub_shipment_date")])))][$row[csf('count_id')]][$comp_type]=strtotime(date("Y-m",strtotime($row[csf("pub_shipment_date")])));
		
	}
	sort($yarnCount_arr);
	sort($summaryBtnMonth_arr);
	
	$jobSpan_arr=array(); $poSpan_arr=array();
	foreach($dtlsData_arr as $yearMonth=>$yearMonth_data)
	{
		foreach($yearMonth_data as $jobNo=>$job_data)
		{
			$jobspan=0;
			foreach($job_data as $poId=>$po_data)
			{
				$poSpan=0;
				foreach($po_data as $copmType_id=>$copmType_data)
				{
					$jobspan++;
					$poSpan++;
				}
				//$jobspan++;
				$poSpan_arr[$yearMonth][$jobNo][$poId]=$poSpan;
			}
			$jobSpan_arr[$yearMonth][$jobNo]=$jobspan;
		}
	}
		
	$td_with=70*count($yarnCount_arr);
	if($type==2) $dtlsDispaly="none"; else $dtlsDispaly="";
	ob_start();
	?>
    <div style="display:<? echo $dtlsDispaly; ?>">
    <fieldset style="width:<? echo $td_with+970; ?>px;">
    	<table width="<? echo $td_with+970;?>">
            <tr class="form_caption">
                <td colspan="<? echo count($yarnCount_arr)+11;?>" align="center"><strong><?php echo $report_title; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo count($yarnCount_arr)+11;?>" align="center">
                	<strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
                </td>
            </tr>
        </table>
        <table class="rpt_table" width="<? echo $td_with+970;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="30" rowspan="2">SL</th>
                    <th width="100" rowspan="2">Buyer</th>
                    <th width="60" rowspan="2">Year</th>
                    <th width="80" rowspan="2">Budget App. Status</th>
                    <th width="70" rowspan="2">Job No</th>
                    <th width="80" rowspan="2">Int. Ref.</th>
                    <th width="120" rowspan="2">Order No</th>
                    <th width="70" rowspan="2">Order Recv. Date</th>
                    <th width="70" rowspan="2">Shipment Date</th>
                    <th width="100" rowspan="2">Yarn Composition</th>
                    <th width="100" rowspan="2">Yarn Type</th>
                    <th align="center" colspan="<? echo count($yarnCount_arr);?>">Yarn Count</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
					<?
                    foreach($yarnCount_arr as $count_id)
                    {
						?>
						<th width="70" style="word-break:break-all"><? echo $yarn_count_arr[$count_id];?></th>
						<?
                    }
                    ?>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo $td_with+990; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_with+970;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
			if($db_type==0) $jobYearCond="and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $jobYearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year";

			$condition= new condition();
			$condition->company_name("=$company_name");
			if(str_replace("'","",$cbo_buyer_name)>0)
			{
				$condition->buyer_name("=$cbo_buyer_name");
			}
			
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="" && $cbo_date_type==1)
			{
				$condition->pub_shipment_date(" between $txt_date_from and $txt_date_to");
			}
			
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="" && $cbo_date_type==2)
			{
				$condition->po_received_date(" between $txt_date_from and $txt_date_to");
			}
			
			if(str_replace("'","",$cbo_year)!=0)
			{
				$condition->job_year("$jobYearCond"); 
			}
			
			if(str_replace("'","",$txt_job_no)!='')
			{
				$condition->job_no_prefix_num("in($txt_job_no)"); 
			}
			
			if(str_replace("'","",$txt_int_ref)!='')
			{
				$txt_int_ref = "'".$txt_int_ref."'";
				$condition->grouping("=$txt_int_ref"); 
			}
			
			if(str_replace("'","",$hide_order_id)!='')
			{
				$orderId = str_replace("'","",$hide_order_id);
				$condition->po_id("in ($orderId)");
			}
			
			if(str_replace("'","",$txt_order_no)!='')
			{
				$condition->po_number("in ($po_num_cond)");
			}
			
			$condition->init();
			$yarn= new yarn($condition);
			//echo $yarn->getQuery(); die;
			$yarnCostingQty_arr=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyArray();
			//echo "<pre>";
			//print_r($yarnCostingQty_arr); die;
			
			$i=1; $m=1; $countMonth_arr=array(); $countQty_arr=array(); $summaryArr=array(); $monthSummary_arr=array();
			foreach($dtlsData_arr as $yrMth=>$yearMonth_data)
			{
				foreach($yearMonth_data as $jobNo=>$job_data)
				{
					$j=1; $jobSpan=0;
					$njobSpan=$jobSpan_arr[$yrMth][$jobNo];
					foreach($job_data as $poId=>$po_data)
					{
						$k=1; $poSpan=0;
						$poSpan=$poSpan_arr[$yrMth][$jobNo][$poId];
						foreach($po_data as $copmType_id=>$copmType_data)
						{
							$comp_id=$ytype_id="";
							$exCompType=explode("__",$copmType_id);
							
							$comp_id=$exCompType[0];
							$ytype_id=$exCompType[1];
							
							$exData=explode("**",$copmType_data['str']);
							
							$buyer_name=$year=$approved=$job_no_prefix_num=$po_number=$pub_shipment_date=$po_received_date=$grouping=$yearMonth='';
							$buyer_name=$exData[0];
							$year=$exData[1];
							$approved=$exData[2];
							$job_no_prefix_num=$exData[3];
							$po_number=$exData[4];
							$pub_shipment_date=$exData[5];
							$po_received_date=$exData[6];
							$grouping=$exData[7];
							
							$budget_app='';
							if($approved==1) $budget_app='Approved'; else if($approved==3) $budget_app='Partial-Approved'; else $budget_app='Un-Approved';
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
								<?php if($j==1) { ?>
									<td width="30" align="center" rowspan="<? echo $njobSpan; ?>"><? echo $i; $i++;?></td>
									<td width="100" rowspan="<? echo $njobSpan; ?>" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
									<td width="60" rowspan="<? echo $njobSpan; ?>"><? echo $year; ?></td>
									<td width="80" rowspan="<? echo $njobSpan; ?>" style="word-break:break-all"><? echo $budget_app; ?></td>
									<td width="70" rowspan="<? echo $njobSpan; ?>" align="center"><? echo $job_no_prefix_num; ?></td>
								<?php } if($k==1) { ?>
									<td width="80" rowspan="<? echo $poSpan; ?>" style="word-break:break-all"><? echo $grouping;//.'-'.$yrMth.'-'.$jobNo.'-'.$poId; ?></td>
									<td width="120" rowspan="<? echo $poSpan; ?>" style="word-break:break-all"><? echo $po_number; ?></td>
									<td width="70" rowspan="<? echo $poSpan; ?>" style="word-break:break-all"><? echo change_date_format($po_received_date); ?></td>
									<td width="70" rowspan="<? echo $poSpan; ?>" style="word-break:break-all"><? echo change_date_format($pub_shipment_date); ?></td>
								<?php } ?>
								<td width="100" style="word-break:break-all"><? echo $composition[$comp_id]; ?></td>
								<td width="100" style="word-break:break-all"><? echo $yarn_type[$ytype_id]; ?></td>
								<?
								$rowQty=0;
								foreach($yarnCount_arr as $count_id)
								{
									$countQty=0;
									$countQty=$yarnCostingQty_arr[$poId][$count_id][$comp_id][100][$ytype_id];
									$rowQty+=$countQty;
									$countMonth_arr[$yrMth][$count_id]+=$countQty;
									$countQty_arr[$count_id]+=$countQty;
									$summaryArr[$count_id][$comp_id][$ytype_id]+=$countQty;
									$monthSummary_arr[date("Y-m",strtotime($pub_shipment_date))][$count_id][$comp_id][$ytype_id]+=$countQty;
									if($countQty>0)
										$tdcountQty=number_format($countQty,2);
									else
										$tdcountQty=0;
									?>
									<td width="70" align="right" style="word-break:break-all"><? echo $tdcountQty; ?></td>
									<?
								}
								?>
								<td align="right" style="word-break:break-all"><? echo number_format($rowQty,2); ?></td>
							</tr>
							<?
							$total_yarn_qty+=$tot_yarn_qty;
							$monthTotal+=$rowQty;
							//$i++;
							$j++;
							$k++;
						}
					}
				}
				?>
					<tr class="tbl_bottom">
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
						<td align="right"><b>Month Total:</b></td>
						<?php
						foreach($yarnCount_arr as $count_id)
						{
							?>
							<td align="right" style="word-break:break-all"><?=number_format($countMonth_arr[$yrMth][$count_id],2); ?></td>
							<?	
						}
						?>
						<td style="word-break:break-all" align="right"><?=number_format($monthTotal,2); ?></td>
					</tr>
				<?
				$grandTotal+=$monthTotal;
				unset($countMonth_arr);
				$monthTotal=0;
			}
			?>
            </table>
        </div>
        <table class="rpt_table" width="<? echo $td_with+970; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr class="tbl_bottom">
                <td width="30">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="70">&nbsp;</td>
                
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><b>Grand Total:</b></td>
                <?php
                foreach($yarnCount_arr as $count_id)
                {
                    ?>
                    <td width="70" align="right" style="word-break:break-all"><? echo number_format($countQty_arr[$count_id],2); ?></td>
                    <?	
                }
                ?>
                <td style="word-break:break-all" align="right"><?=number_format($grandTotal,2); ?></td>
            </tr>
        </table>
        </fieldset>
        <br>
        <table align="left" width="500" cellpadding="0" cellspacing="0">
        	<tr>
                <td colspan="4">
                    <table class="rpt_table" align="left" width="500" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <caption><b>Yarn Summary</b></caption>
                        <thead>
                            <th width="100">Yarn Count</th>
                            <th width="200">Yarn Composition</th>
                            <th width="100">Yarn Type</th>
                            <th>Req. Qty</th>
                        </thead>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div align="left" style="width:500px; max-height:200px; overflow-y:scroll" id="scroll_body1">
                        <table class="rpt_table" align="left" width="480" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                            <?
                            $k=1; $total_yarn_summary=0;
                            foreach($summaryArr as $countId=>$countData)
                            {
                                foreach($countData as $compId=>$compData)
                                {
                                    foreach($compData as $typeId=>$reqQty)
                                    {
										if($reqQty!=0)
										{
											?>
											<tr>
												<td width="100" style="word-break:break-all"><?=$yarn_count_arr[$countId];?></td>
												<td width="200" style="word-break:break-all"><?=$composition[$compId];?></td>
												<td width="100" style="word-break:break-all"><?=$yarn_type[$typeId];?></td>
												<td align="right"><?=number_format($reqQty,2);?></td>
											</tr>
											<?
											$k++;
											$total_yarn_summary+=$reqQty;
										}
                                    }
                                }
                            }
                            ?>
                        </table>
                    </div>
            	</td>
            </tr>
            <tr class="tbl_bottom">
            	<td width="100">&nbsp;</td>
                <td width="200">&nbsp;</td>
                <td width="100">Total:</td>
                <td align="right"><?=number_format($total_yarn_summary,2);?></td>
            </tr>
        </table>
        </div>
        <?
		if($type==2) 
		{ 
			ob_clean(); ob_start(); 
			$td_month=80*count($summaryBtnMonth_arr);
			?>
			<div>
				<fieldset style="width:<?=$td_month+380; ?>px;">
					<table width="<?=$td_month+380;?>">
						<tr class="form_caption">
							<td colspan="<?=count($summaryBtnMonth_arr)+4;?>" align="center"><strong><?php echo $report_title.' [Yarn Requirement Summary]'; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td colspan="<? echo count($summaryBtnMonth_arr)+4;?>" align="center">
								<strong><? echo $company_arr[$company_name];?></strong>
								<br>
								<strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
							</td>
						</tr>
					</table>
                    <table class="rpt_table" width="<? echo $td_month+380;?>" cellpadding="0" cellspacing="0" border="1" rules="all" style="font-size:12px">
                        <thead>
                            <tr>
                                <th width="60" rowspan="2">Yarn Count</th>
                                <th width="120" rowspan="2">Yarn Composition</th>
                                <th width="120" rowspan="2">Yarn Type</th>
                                <th width="80" rowspan="2">Total Req. Qty</th>
                            </tr>
                            <tr>
                                <?
                                foreach($summaryBtnMonth_arr as $monthyear)
                                {
                                    ?>
                                    <th width="80" style="word-break:break-all"><?=date("M-y",strtotime($monthyear));?></th>
                                    <?
                                }
                                ?>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:<? echo $td_month+400; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
						<table class="rpt_table" width="<? echo $td_month+380; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" style="font-size:12px">
                        <?
							$k=1; $monthSumm_arr=array(); $total_yarn_summary=0;
							foreach($summaryArr as $countId=>$countData)
                            {
                                foreach($countData as $compId=>$compData)
                                {
                                    foreach($compData as $typeId=>$reqQty)
                                    {
										if($reqQty!=0)
										{
											if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>">
												<td width="60" style="word-break:break-all"><?=$yarn_count_arr[$countId];?>&nbsp;</td>
												<td width="120" style="word-break:break-all"><?=$composition[$compId]; ?>&nbsp;</td>
												<td width="120" style="word-break:break-all"><?=$yarn_type[$typeId];?>&nbsp;</td>
												<td width="80" style="word-break:break-all" align="right"><?=number_format($reqQty,2);?></td>
												<?
												foreach($summaryBtnMonth_arr as $monthyear)
												{
													$monthQty=0;
													$monthQty=$monthSummary_arr[$monthyear][$countId][$compId][$typeId];
													$monthSumm_arr[$monthyear]+=$monthQty;
													?>
													<td width="80" style="word-break:break-all" align="right"><? if($monthQty!=0) echo number_format($monthQty,2); else echo ''; ?></td>
													<?
												}
												?>
											</tr>
											<?
											$k++;
											$total_yarn_summary+=$reqQty;
										}
                                    }
                                }
                            }
							?>
						</table>
					</div>
                    <table class="rpt_table" width="<? echo $td_month+380; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" style="font-size:12px">
                        <tr class="tbl_bottom">
                            <td width="60">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="120"><b>Total:</b></td>
                            <td width="80" align="right"><?=number_format($total_yarn_summary,2); ?></td>
                            <?php
                            foreach($summaryBtnMonth_arr as $monthyear)
                            {
                                ?>
                                <td width="80" align="right" style="word-break:break-all"><?=number_format($monthSumm_arr[$monthyear],2); ?></td>
                                <?	
                            }
                            ?>
                        </tr>
                    </table>   
                </fieldset>
            </div>       
        	<?
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
		echo "$html****$filename****$type"; 
		exit();
    }
?>