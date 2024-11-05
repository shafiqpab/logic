<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "- Team Member-", $selected, "" ); 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_style_name = new Array();
	 
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value( strcon )
	{
		//alert(strcon);
		strconArr=strcon.split("_");
		$('#txt_job_no').val( strconArr[1] );
		$('#hidden_job_id').val( strconArr[0]);
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="txt_job_no" />
     <input type="hidden" id="hidden_job_id" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		$year_select="year(insert_date) as year";
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
		$year_select="to_char(insert_date,'YYYY') as year";
	}
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
    $season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	
	 $sql= "select id, $year_select,job_no, job_no_prefix_num, style_ref_no, buyer_name,season_buyer_wise from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $year_cond  order by id DESC ";
	
	//echo $sql;die;
	
	$arr=array(1=>$buyer_library,3=>$marchentrArr,4=> $season_library);
	echo  create_list_view("list_view", "Year,Buyer,Job No,Style Ref.,Season", "100,100,130,150,100","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,buyer_name,0,0,season_buyer_wise", $arr , "year,buyer_name,job_no_prefix_num,style_ref_no,season_buyer_wise", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','',"") ;
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );

$sub_dept_array = return_library_array("select id, sub_department_name from lib_pro_sub_deparatment","id","sub_department_name");
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
$country_code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code"  );

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if( $action=="report_generate" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
    $cbo_season=str_replace("'","",$cbo_season);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	if($cbo_season!=0) $season_cond="and a.season_buyer_wise=$cbo_season "; else $season_cond="";

	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to); 
		$date_cond=" and b.po_received_date between '$start_date' and '$end_date'";
	}
	
	if($db_type==0)
	{
		if(str_replace("'","",$cbo_year)!=0) $yearCond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $yearCond="";
	}
	else
	{
		if(str_replace("'","",$cbo_year)!=0) $yearCond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $yearCond="";
	}
	$hidden_job_id=str_replace("'","",$hidden_job_id);
	if($hidden_job_id)
	{
		$hidden_job_id_cond="and a.id in($hidden_job_id)";
	}
	else $hidden_job_id_cond="";
	
	if(str_replace("'","",$txt_job_no)!="" || str_replace("'","",$txt_job_no)!=0) 
	$jobcond="and a.job_no_prefix_num=".$txt_job_no."";
	 else $jobcond="";
	
	 
	
	if($template==1)
	{
		ob_start();
		$width=2160;
	?>
        <div style="width:<? echo $width;?>px">
        <fieldset style="width:100%;">
          <table width="<? echo $width;?>">
              <tr class="form_caption">
                    <td colspan="20" align="center"><? echo $report_title;?></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="20" align="center"><? echo $company_library[$company_name]; ?></td>
                </tr>
            </table>
            <table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="40" style="word-break: break-all;">SL</th>
                    <th width="120" style="word-break: break-all;">LC Company</th>
                    <th width="100" style="word-break: break-all;">Buyer</th>
                    <th width="100" style="word-wrap: break-word;">Style Ref.</th>
                    <th width="100" style="word-break: break-all;">Job No</th>
                    <th width="100" style="word-wrap: break-word;">Style Desc.</th>
                    <th width="100" style="word-break: break-all;">Season</th>
                    <th width="70" style="word-wrap: break-word;">Product Dept.</th>
                    <th width="90" style="word-break: break-all;">Department</th>
                    <th width="100" style="word-break: break-all;">Item </th>
                    <th width="100" style="word-break: break-all;">SMV </th>
                    <th width="100" style="word-break: break-all;">PO No</th>
                    <th width="70" style="word-wrap: break-word;">Order Receive Date</th>
                    <th width="70" style="word-wrap: break-word;">Shipment Date</th>
                    
                    <th width="70" style="word-wrap: break-word;">Delivery Country</th>
                    <th width="70" style="word-wrap: break-word;">UCUST Code</th>
                    
                    <th width="70" style="word-break: break-all;">Country</th>
                    <th width="70" style="word-break: break-all;">Cust Code</th>
                    
                    <th width="70" style="word-wrap: break-word;">Order UOM</th>
                    <th width="70" style="word-wrap: break-word;">Ship Mode</th>
                    <th width="100" style="word-break: break-all;">Color</th>
                    <th width="70" style="word-break: break-all;">Size</th>
                    <th width="70" style="word-wrap: break-word;">Order Qty</th>
                    <th width="70" style="word-wrap: break-word;">Need To Cut</th>
                    <th width="70" style="word-break: break-all;">Price</th>
                    <th width="" style="word-break: break-all;">Remarks</th>
                     
                     
                </thead>
            </table>
            <div style="width:<? echo $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
                $i=1; $total_order_qnty=0; $total_order_qnty_in_pcs=0;  
				 
				if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
				else $year_field="";//defined Later
               $sql="SELECT a.job_no_prefix_num, a.job_no, $year_field, a.company_name,a.style_owner,a.order_uom,a.ship_mode,a.buyer_name,a.season_year,a.season_buyer_wise, a.team_leader, a.dealing_marchant, a.style_ref_no, a.style_description,a.product_code,a.product_dept,a.pro_sub_dep,a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number,b.grouping,b.file_no,b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut,c.item_number_id,c.color_number_id,c.size_number_id,c.country_id,c.order_quantity,c.size_order,c.code_id,c.ul_country_code,c.ultimate_country_id,c.plan_cut_qnty,c.country_id,c.order_rate,c.order_total, a.set_smv,b.details_remarks from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $jobcond $yearCond $date_cond $buyer_id_cond $hidden_job_id_cond $date_cond $season_cond order by  b.id,c.color_order,c.size_order";// b.id, b.pub_shipment_date
			   //echo $sql;die;
                $nameArray=sql_select($sql);
                $tot_rows=count($nameArray);
				$total_plan_cut_anty=$total_order_qnty=0;
                foreach($nameArray as $row )
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$seasonyear=substr($row[csf('season_year')], -2);
					$season_year="";
					if($seasonyear)
					{
						$season_year="-".$seasonyear;
					}
                ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="120" style="word-break: break-all;" align="center"><p ><? echo $company_library[$row[csf('style_owner')]]; ?></p></td>
                        <td width="100" style="word-break: break-all;" align="center"><p ><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                        <td width="100" style="word-break: break-all;"><p ><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="100" style="word-break: break-all;"><p ><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="100" style="word-break: break-all;" align="center"><p ><? echo $row[csf('style_description')]; ?></p></td>
                        <td width="100" style="word-break: break-all;" align="center"><p ><? echo $season_library[$row[csf('season_buyer_wise')]].$season_year; ?></p></td>
                        <td width="70" style="word-break: break-all;"><p ><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
                        <td width="90" style="word-break: break-all;"><p ><? echo $row[csf('product_code')]; ?></p></td>
                        <td width="100" style="word-break: break-all;"><p ><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="100" align="center" style="word-break: break-all;"><p ><? echo number_format($row[csf('set_smv')],2); ?></p></td>
                        <td width="100" style="word-break: break-all;"><p ><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
                        <td width="70" align="center" style="word-break: break-all;" >
                            <p ><? 
                                echo change_date_format($row[csf('po_received_date')]); //
                            ?></p>
                        </td>
                        <td width="70" align="center" style="word-break: break-all;"><p ><? echo  change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
                         <td width="70" align="center" style="word-break: break-all;"><p ><? echo  $country_arr[$row[csf('country_id')]]; ?></p></td>
                         <td width="70" align="center" style="word-break: break-all;"><p ><? echo $country_code_arr[$row[csf('code_id')]]; ?></p></td>
                          
                        <td width="70" align="center" style="word-break: break-all;">
	                        <p >
	                        	<? 
									echo  $country_arr[$row[csf('ultimate_country_id')]];//ultimate_country_id
		                        ?> 
		                    </p>
                        </td>
                        <td width="70" align="center" style="word-break: break-all;"><p ><? echo $country_code_arr[$row[csf('ul_country_code')]]; ?></p></td>
                        
                        <td width="70" align="center" style="word-break: break-all;"><p ><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                        
                        <td width="70" align="center" style="word-break: break-all;"><p ><? echo $shipment_mode[$row[csf('ship_mode')]];; ?></p></td>
                        <td width="100" align="center" style="word-break: break-all;"><p ><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="70" align="center" style="word-break: break-all;"><p ><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                        <td width="70" align="right" style="word-break: break-all;"><p ><? echo number_format($row[csf('order_quantity')],0,'.',''); ?></p></td>
                        <td width="70" align="right" style="word-break: break-all;"><? echo number_format($row[csf('plan_cut_qnty')],0,'.',''); ?></td>
                        <td width="70" align="right" style="word-break: break-all;"><p ><? echo number_format($row[csf('order_rate')],2,'.',''); ?></p></td>
                        <td width="" style="word-break: break-all;" align="center"><p ><? echo $row[csf('details_remarks')]; ?></p></td>
                     
                    </tr>
                <?
				$total_order_qnty+=$row[csf('order_quantity')];
				$total_plan_cut_anty+=$row[csf('plan_cut_qnty')];
                $i++;
                }
                ?>
                </table>
                <table class="rpt_table" width="<? echo $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <th width="40"></th>
                        <th width="120"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70"></th>
                        <th width="90"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" align="right"></th>
                        <th width="70" align="right" id=""></th>
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="70">&nbsp;</th>
                         <th width="70"></th>
                        <th width="70"></th>
                        <th width="100"></th>
                        <th width="70">Total</th>
                        <th width="70"  align="right" id="total_order_qnty_in_pcs"><? echo $total_order_qnty;?></th>
                         <th width="70"  align="right" id="total_order_qnty_needtocut"><? echo $total_plan_cut_anty;?></th>
                        <th width="70"></th>
                        <th width=""></th>
                    </tfoot>
                </table>
            </div>
          
           
            </fieldset>
        </div>
<?
	}
	 
	echo "$total_data****requires/$filename****$tot_rows";
	exit();	
}

if($action=="trims_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
    <div>
        <fieldset style="width:600px;">
        <div style="width:600px" align="center">	
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="130">Item Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="80">Rate</th>
                    <th width="110">Trims Cost/Dzn</th>
                    <th>Total Trims Cost</th>
                </thead>
            </table>
            </div>
            <div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                        
					$dzn_qnty=0;
					if($costing_per==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					//and b.po_break_down_id='$po_id' 
					$sql="select a.trim_group, a.amount,a.rate, a.cons_dzn_gmts as cons from wo_pre_cost_trim_cost_dtls a where   a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0";
					$trimsArray=sql_select($sql);
					$i=1;
					foreach($trimsArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="130"><div style="width:130px; word-wrap:break-word"><? echo $item_library[$row[csf('trim_group')]]; ?></div></td>
							<td width="90" align="right"><? echo number_format($row[csf('cons')],2); ?></td>
							<td width="80" align="right"><? echo number_format($row[csf('rate')],2); ?></td>
							<td width="110" align="right">
								<?
                                    $trims_cost_per_dzn=$row[csf('cons')]*$row[csf('rate')]; 
                                    echo number_format($trims_cost_per_dzn,2);
									$tot_trims_cost_per_dzn+=$trims_cost_per_dzn; 
                                ?>
                            </td>
							<td align="right">
								<?
                                	$trims_cost=($po_qnty/$dzn_qnty)*$trims_cost_per_dzn;
									echo number_format($trims_cost,2);
									$tot_trims_cost+=$trims_cost;
                                ?>
                            </td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="4">Total</th>
                        <th><? echo number_format($tot_trims_cost_per_dzn,2); ?></th>
                        <th><? echo number_format($tot_trims_cost,2); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
}

if($action=="other_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
    <div align="center">
        <fieldset style="width:600px;">
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <th width="200">Particulars</th>
                    <th width="90">Cost/Dzn</th>
                    <th>Total Cost</th>
                </thead>
				<?
                $costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                    
                $dzn_qnty=0;
                if($costing_per==1)
                {
                    $dzn_qnty=12;
                }
                else if($costing_per==3)
                {
                    $dzn_qnty=12*2;
                }
                else if($costing_per==4)
                {
                    $dzn_qnty=12*3;
                }
                else if($costing_per==5)
                {
                    $dzn_qnty=12*4;
                }
                else
                {
                    $dzn_qnty=1;
                }
                    
                $sql="select common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0";
                $fabriccostArray=sql_select($sql);
                ?>
                <tr bgcolor="#E9F3FF">
                    <td>Commercial Cost</td>
                    <td align="right"><? echo number_format($fabriccostArray[0][csf('comm_cost')],2); ?></td>
                    <td align="right">
                        <?
                            $comm_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('comm_cost')]; 
                            echo number_format($comm_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Lab Test Cost</td>
                    <td align="right"><? echo number_format($fabriccostArray[0][csf('lab_test')],2); ?></td>
                    <td align="right">
                        <?
                            $lab_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('lab_test')]; 
                            echo number_format($lab_cost,2);
                        ?>
                    </td>
                </tr>
                 <tr bgcolor="#E9F3FF">
                    <td>Inspection Cost</td>
                    <td align="right"><? echo number_format($fabriccostArray[0][csf('inspection')],2); ?></td>
                    <td align="right">
                        <?
                            $inspection_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('inspection')]; 
                            echo number_format($inspection_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Freight Cost</td>
                    <td align="right"><? echo number_format($fabriccostArray[0][csf('freight')],2); ?></td>
                    <td align="right">
                        <?
                            $freight_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('freight')]; 
                            echo number_format($freight_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>Common OH Cost</td>
                    <td align="right"><? echo number_format($fabriccostArray[0][csf('common_oh')],2); ?></td>
                    <td align="right">
                        <?
                            $common_oh_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('common_oh')]; 
                            echo number_format($common_oh_cost,2);
							
							$tot_cost_per_dzn=$fabriccostArray[0][csf('comm_cost')]+$fabriccostArray[0][csf('lab_test')]+$fabriccostArray[0][csf('inspection')]+$fabriccostArray[0][csf('freight')]+$fabriccostArray[0][csf('common_oh')];
							$tot_cost=$comm_cost+$lab_cost+$inspection_cost+$freight_cost+$common_oh_cost;
                        ?>
                    </td>
                </tr>
                <tfoot>
                    <th>Total</th>
                    <th><? echo number_format($tot_cost_per_dzn,2); ?></th>
                    <th><? echo number_format($tot_cost,2); ?></th>
                </tfoot>    
            </table>
        </fieldset>
    </div>
<?
}
disconnect($con);
?>