<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($tittle." No Info", "../../../", 1, 1,'','','');	
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}
		
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str )
		{			
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 )
			{
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_id').val( id );
			$('#hide_no').val( name );
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
	                    <th id="search_by_td_up" width="170">Please Enter <? echo $tittle; ?> No </th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
	                    <input type="hidden" name="hide_no" id="hide_no" value="" />
	                    <input type="hidden" name="hide_id" id="hide_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyerID,"",0 );
								?>
	                        </td>
	                        <td align="center">
	                    	<?
							    $search_by_arr=array(1=>"Quotation No",2=>"Style Ref",3=>"MKT. No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>
	                        <td align="center" id="search_by_td">
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
	                        </td> 	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_quotation_style_no_search_list_view', 'search_div', 'price_quotation_approval_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                        </td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit(); 
}

if($action=="create_quotation_style_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($search_by==1)
	{
		$search_field="id"; 
	} else if($search_by==2)
	{
		$search_field="style_ref"; 
	}else
	{
		$search_field="mkt_no";
	} 
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$data[1]";
	}		
	
	$sql= "SELECT id as quotation_no, company_id, buyer_id, style_ref, mkt_no from wo_price_quotation where status_active=1 and is_deleted=0 and company_id=$company_id and $search_field like '$search_string' $buyer_id_cond order by id";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Quotation No, MKT NO,Style Ref. No", "120,120,120,100,120","700","240",0, $sql , "js_set_value", "quotation_no,mkt_no,style_ref", "", 1, "company_id,buyer_id,0,0,0", $arr , "company_id,buyer_id,quotation_no,mkt_no,style_ref", "",'','0,0,0,0,0','',1);
   exit();
} 

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	
	extract(check_magic_quote_gpc( $process ));

	$type = str_replace("'","",$cbo_type);
	$txt_quotation_no = str_replace("'","",$txt_quotation_no);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);

 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and quot_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
			$date_cond=" and insert_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
		}
	}

	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	
	$quotation_cond=""; $buyer_id_cond="";
	if($txt_quotation_no != "") $quotation_cond=" and id in(".implode("','",explode("*",$txt_quotation_no)).")";
	if($cbo_buyer_name != 0) $buyer_id_cond=" and buyer_id=$cbo_buyer_name";
	
	$dealing_merchant_array = return_library_array("SELECT id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$designation_array = return_library_array( "SELECT id, custom_designation from lib_designation", "id", "custom_designation" );

	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=2 and report_id=32 and is_deleted=0 and status_active=1");

	$print_report_format_ids=explode(",",$print_report_format);
	
	$user_name_array = array();
	$userData = sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}

	$approved_no_array = array();
	$queryApp = "SELECT mst_id, approved_by, max(approved_no) as approved_no from approval_history where entry_form=10 and un_approved_by=0 group by mst_id, approved_by";
	$resultApp = sql_select($queryApp);
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_no')];
	}
	
	$buyer_id_arr = return_library_array( "SELECT user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=10 and bypass=2", "user_id", "buyer_id" );	
	$signatory_data_arr = sql_select("SELECT user_id, sequence_no, bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=10 order by sequence_no");
	foreach($signatory_data_arr as $sval)
	{
		$signatory_main[$sval[csf('user_id')]]=$sval[csf('bypass')];
		$userArr[$sval[csf('user_id')]]=$sval[csf('user_id')];
	}

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array();
	$query = "SELECT mst_id, approved_no, approved_by, approved_date, user_ip, entry_form from approval_history where entry_form=10 ".where_con_using_array($userArr,1,'approved_by')." and un_approved_by=0 order by id asc";

	$result = sql_select($query);
	foreach($result as $row)
	{
		$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_by')]] = $row[csf('approved_date')];
		$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_by')]] = $row[csf('user_ip')];
		$approved_date = date("Y-m-d",strtotime($row[csf('approved_date')]));
		$user_approval_mst_count[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_by')];
		
		if($max_approval_date_array[$row[csf('mst_id')]]=="")
		{
			$max_approval_date_array[$row[csf('mst_id')]]=$approved_date;
		}
		else
		{
			if($approved_date>$max_approval_date_array[$row[csf('mst_id')]])
			{
				$max_approval_date_array[$row[csf('mst_id')]]=$approved_date;
			}
		}
	}
	// echo '<pre>';print_r($user_approval_mst_count);
	ob_start();
	?>
        <fieldset style="width:1320px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table" align="left">
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Quotation No</th>
                    <th width="80">Style Ref</th>
                    <th width="80">MKT No</th>
                    <th width="80">Buyer Name</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Image</th>
                    <th width="120">Quot. Date</th>
                    <th width="140">Signatory</th>
                    <th width="130">Designation</th>
                    <th  width="50">Can Bypass</th>
                    <th width="100">IP Address</th>
                    <th width="100">Approval Date</th>
                    <th width="100">Approval Time</th>
                    <th width="100">Approve No</th>
                </thead>
            </table>
			<div style="width:1320px; overflow-y:scroll; max-height:310px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
						$rowspanMain=count($signatory_main);						
						$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
						
						// if($type==2) $approved_cond=" and approved=1";
						// elseif($type==1) $approved_cond=" and approved=3";
						// elseif($type==0) $approved_cond="";
						// else $approved_cond=" and approved in (0,2)";
						
						$sql="SELECT id as quotation_id, company_id, buyer_id, dealing_merchant, quot_date, approved, insert_date, mkt_no,style_ref from  wo_price_quotation where company_id=$cbo_company_name and ready_to_approved=1 and status_active=1 and is_deleted=0 $quotation_cond $buyer_id_cond $date_cond  group by id, company_id, buyer_id, dealing_merchant, quot_date, approved, insert_date,style_ref, mkt_no order by insert_date desc";
						// echo $sql;
                        $sql_result=sql_select($sql);


						$price_quotation_app_arr=array();
						foreach ($sql_result as $row)
                        {
							$user_app=count($user_approval_mst_count[$row[csf('quotation_id')]]);
							$user_no=count($userArr);						
							if($user_no==$user_app && $type==3) {
								$app_type=3;//Full Approved
							}elseif ($user_no>$user_app && $row[csf('approved')] >0 && $type==2) {
							   $app_type=2;//partial
							}elseif ($user_no>$user_app && $row[csf('approved')]==0 && $type==1 ) {
							   $app_type=1;//pending
							}else{
							   $app_type=0;
							}
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['buyer_id']=$row[csf('buyer_id')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['quotation_id']=$row[csf('quotation_id')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['insert_date']=$row[csf('insert_date')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['supplier_id']=$row[csf('supplier_id')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['quot_date']=$row[csf('quot_date')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['inserted_by']=$row[csf('inserted_by')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['approved']=$row[csf('approved')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['dealing_merchant']=$row[csf('dealing_merchant')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['style_ref']=$row[csf('style_ref')];
							$price_quotation_app_arr[$app_type][$row[csf('quotation_id')]]['mkt_no']=$row[csf('mkt_no')];
						}





						foreach ($price_quotation_app_arr[$type] as $q_id=>$row)
                        {
							$full_approval='';
							$rowspan=$rowspanMain;

							$company_id = str_replace("'","",$cbo_company_name);
							$report_action = "generate_report";

							if(count($print_report_format_ids) > 0){
						        if($print_report_format_ids[0] == 90 ){

						        	$type="preCostRpt";
						    		$report_data_1 = '&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id'].'&txt_style_ref='.$row['style_ref'].'&txt_quotation_date='.$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 91 )
						        { 
						        	$type="preCostRpt2";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 92 )
						        { 
						        	$type="preCostRpt3";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 137 )
						        { 
						        	$type="preCostRpt11";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=0'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 191 )
						        { 
						        	$type="preCostRpt12";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 194 )
						        { 
						        	$type="";
						        	$report_data_1="";
						        }
						        if($print_report_format_ids[0] == 213 )
						        { 
						        	$type="";
						        	$report_data_1="";
						        }
						        if($print_report_format_ids[0] == 217 )
						        { 
						        	// not done... lc cost dtls
						        	$type=6;
						    		$report_data_1 = '../../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php?&reporttype='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action=report_generate&comments_head=1';
						        }
						        if($print_report_format_ids[0] == 219 )
						        { 
						        	$type="preCostRpt4";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 239 )
						        { 
						        	// not done... summary 2
						        	$type=5;
						    		$report_data_1 = '../../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php?&reporttype='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action=report_generate&comments_head=0';
						        }
						        if($print_report_format_ids[0] == 275 )
						        { 
						        	$type="preCostRpt5";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 308 )
						        { 
						        	$type="";
						        	$report_data_1="";
						        }
						        if($print_report_format_ids[0] == 336 )
						        { 
						        	$type="";
						        	$report_data_1="";
						        }
						        if($print_report_format_ids[0] == 406 )
						        { 
						        	$type="buyerSubmitSummery";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						        if($print_report_format_ids[0] == 414 )
						        { 
						        	$type="preCostRpt6";
						    		$report_data_1 = '../../order/woven_order/requires/quotation_entry_controller.php?&type='.$type.'&cbo_company_name='.$company_id.'&txt_quotation_id='.$row['quotation_id'].'&cbo_buyer_name='.$row['buyer_id']."&txt_style_ref='".$row['style_ref']."'&txt_quotation_date=".$row['insert_date'].'&zero_value=1'.'&action='.$report_action;
						        }
						    }

						    $html ='<a href="'.$report_data_1.'" target="_blank">'.$row['quotation_id'].'</a>';
							
							$full_approval=true; $approvedStatus="";
							foreach($bypass_no_user_id_main as $uId)
							{
								$buyer_ids=$buyer_id_arr[$uId];
								$buyer_ids_array=explode(",",$buyer_id_arr[$uId]);
							}
							
							if($cbo_date_by==3 && $from_date!="" && $to_date!="")
							{
								$max_approved_date=$max_approval_date_array[$row['quotation_id']];
								if($max_approved_date>=$from_date && $max_approved_date<=$to_date) $print_cond=1;
								else $print_cond=0;
							}
							else
							{ 
								$print_cond=1;
							}
							
													
								$z=0; 
								foreach($signatory_main as $user_id=>$val)
								{
									if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								    ?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<?
									if($z==0)
									{
										if(str_replace("'","",trim($cbo_date_by))==1)
										{
											$date_all="Q Date : ".change_date_format($row['quot_date']);
										}
										else if(str_replace("'","",trim($cbo_date_by))=='2')
										{
											$insert_date=$row['insert_date'];
											$date_all="In Date: ".date("d-m-Y",strtotime($insert_date)); 
										}
									    ?>
										<td width="40" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
										<td valign="middle" width="80" rowspan="<? echo $rowspan; ?>" align="center">
											<? echo $html; ?>
                                        </td>
										<td valign="middle" width="80" rowspan="<? echo $rowspan; ?>" align="center">
										
										<? echo $row['style_ref']; ?>
                                        </td>
										<td valign="middle" width="80" rowspan="<? echo $rowspan; ?>" align="center">
											<? echo $row['mkt_no']; ?>
                                        </td>
										<td valign="middle" width="80" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_arr[$row['buyer_id']]; ?>&nbsp;</p></td>
                                        <td valign="middle" width="110" rowspan="<? echo $rowspan; ?>"><p><? echo $dealing_merchant_array[$row['dealing_merchant']]; ?>&nbsp;</p></td>

                                        <td valign="middle" width="50" rowspan="<? echo $rowspan; ?>" align="center"><a href="##" onClick="openImg('<? echo $row['quotation_id'];?>','show_image');">View</a></td>

										<td valign="middle" width="120" rowspan="<? echo $rowspan; ?>" align="center">
											<? echo $row['quot_date'];	?>
										</td>
									    <?
									}
									
									$approved_no=''; $user_ip='';								
									$approval_date=$user_approval_array[$row['quotation_id']][$user_id];
									$user_ip=$user_ip_array[$row['quotation_id']][$user_id];
									if($approval_date!="") $approved_no=$approved_no_array[$row['quotation_id']][$user_id];					
									
									$date=''; $time=''; 
									if($approval_date!="") 
									{
										$date=date("d-M-Y",strtotime($approval_date)); 
										$time=date("h:i:s A",strtotime($approval_date)); 
									}
									?>
										<td width="140"><p><? echo $user_name_array[$user_id]['full_name']." (".$user_name_array[$user_id]['name'].")"; ?>&nbsp;</p></td>
                                        <td width="130"><p><? echo $user_name_array[$user_id]['designation']; ?>&nbsp;</p></td>			
                                        <td width="50" align="center"><p><? echo $yes_no[$val]; ?>&nbsp;</p></td>
                                        <td width="100" align="center"><p><? echo $user_ip; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? if($row['approved']!=0) echo $date; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? if($row['approved']!=0) echo $time; ?>&nbsp;</p></td>
										<td width="100"><p>&nbsp;<? if($row['approved']!=0) echo $approved_no; ?>&nbsp;</p></td>
									</tr>
								    <?
									$z++;
								}
							    $i++;
							
						}
						?>
                    </tbody>
                </table>
			</div>
      	</fieldset>      
	<?	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 	
}

if($action=="show_image")
{
	echo load_html_head_contents("Image View", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
    ?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$quotation_no' and form_name='quotation_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                        ?>
                    	<td align="center"><img width="300px" height="180px" src="../../../<? echo $row[csf('image_location')];?>" /></td>
                        <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}

?>