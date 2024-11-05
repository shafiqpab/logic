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
if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(3) and c.tag_company =$data and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_quotation_style_no_search_list_view', 'search_div', 'service_work_order_approval_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

	$cbo_year = str_replace("'","",$cbo_year);
	$item_category = str_replace("'","",$cbo_item_category_id);
	$supplier = str_replace("'","",$cbo_supplier);
	$txt_wo_no = str_replace("'","",$txt_wo_no);
	$type = str_replace("'","",$cbo_type);
 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		$date_cond=" and a.wo_date between $txt_date_from and $txt_date_to";
	}

	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	
	$item_category_cond=""; $supplier_cond=""; $wo_no_cond="";
	if($item_category !=0) $item_category_cond=" and b.item_category_id =$item_category";
	if($supplier != 0) $supplier_cond=" and a.supplier_id=$supplier";
	if($txt_wo_no !="") $wo_no_cond="and a.wo_number_prefix_num LIKE '%$txt_wo_no%'";

	if ($cbo_year=="" || $cbo_year==0) $woYearCond="";
	else
	{
		if($db_type==2) $woYearCond=" and to_char(a.insert_date,'YYYY')='".trim($cbo_year)."' ";
		else $woYearCond=" and YEAR(a.insert_date)='".trim($cbo_year)."' ";
	}
	
	$dealing_merchant_array = return_library_array("SELECT id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$designation_array = return_library_array( "SELECT id, custom_designation from lib_designation", "id", "custom_designation" );
	
	$user_name_array = array();
	$userData = sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}

		
	$approved_no_array = array();
	$queryApp = "SELECT mst_id, approved_by, approved_no from approval_history where entry_form=60 and un_approved_by=0";
	$resultApp = sql_select($queryApp);
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_no')];
	}
	



	$buyer_id_arr = return_library_array( "SELECT user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and page_id=2588 and bypass=2", "user_id", "buyer_id" );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");	
	$signatory_data_arr = sql_select("SELECT user_id, sequence_no, bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and page_id=2588 order by sequence_no");
	

	foreach($signatory_data_arr as $sval)
	{
		$signatory_main[$sval[csf('user_id')]]=$sval[csf('bypass')];
		$userArr[$sval[csf('user_id')]]=$sval[csf('user_id')];
	}

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array();
	$query = "SELECT mst_id, approved_no, approved_by, approved_date, user_ip, entry_form from approval_history where entry_form=60 ".where_con_using_array($userArr,1,'approved_by')." and un_approved_by=0";
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
	//echo '<pre>';print_r($user_approval_array);
	ob_start();
	?>
        <fieldset style="width:1020px;">
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" align="left">
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Work Order  No</th>               
                    <th width="80">Supplier Name</th>
					<th width="80">Work Order . Date</th>
                             
               
                    <th width="140">Signatory</th>
                    <th width="130">Designation</th>
                    <th  width="50">Can Bypass</th>
                    <th width="100">IP Address</th>
                    <th width="100">Approval Date</th>
                    <th width="100">Approval Time</th>
                    <th>Approve No</th>
                </thead>
            </table>
			<div style="width:1020px; overflow-y:scroll; max-height:310px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
						$rowspanMain=count($signatory_main);						
						$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
						
						if($type==3) $approved_cond=" and a.is_approved=1";
						elseif($type==1) $approved_cond=" and a.is_approved=0";
						elseif($type==2) $approved_cond=" and a.is_approved=3";
						elseif($type==0) $approved_cond="";
						else $approved_cond=" and a.is_approved in (0,2)";
						
						$sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id,a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no, a.delivery_place, a.location_id,a.payterm_id,a.remarks,a.contact,a.tenor,a.is_approved FROM wo_non_order_info_mst a, wo_non_order_info_dtls c WHERE  a.id = c.mst_id and a.entry_form = 484 and a.company_name=$cbo_company_name and c.item_category_id not in(1,2,3,12,13,14) and a.status_active=1
						   and a.is_deleted=0  $supplier_cond $date_cond $item_category_cond $woYearCond $wo_no_cond ORDER by a.id ";

						// echo $sql;
                        $sql_result=sql_select($sql);
						$stationary_wo_app_arr=array();
						foreach ($sql_result as $row)
                        {
							$user_app=count($user_approval_mst_count[$row[csf('id')]]);
							$user_no=count($userArr);						
							 if($user_no==$user_app && $type==3) {
								 $app_type=3;
							 }elseif ($user_no>$user_app && $user_app>0 && $type==2) {
								$app_type=2;
							 }elseif ($user_no>$user_app && $user_app==0 && $type==1) {
								$app_type=1;
							 }else{
								$app_type=0;
							 }
							$stationary_wo_app_arr[$app_type][$row[csf('id')]]['company_name']=$row[csf('id')];
							$stationary_wo_app_arr[$app_type][$row[csf('id')]]['wo_number']=$row[csf('wo_number')];
							$stationary_wo_app_arr[$app_type][$row[csf('id')]]['quot_date']=$row[csf('quot_date')];
							$stationary_wo_app_arr[$app_type][$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
							$stationary_wo_app_arr[$app_type][$row[csf('id')]]['wo_date']=$row[csf('wo_date')];
							$stationary_wo_app_arr[$app_type][$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
							$stationary_wo_app_arr[$app_type][$row[csf('id')]]['is_approved']=$row[csf('is_approved')];
						}

					// echo '<pre>';print_r($stationary_wo_app_arr[$app_type]);






						foreach ($stationary_wo_app_arr[$type] as $wo_id=>$row)
                        {
							$full_approval='';
							$rowspan=$rowspanMain;
							
							$full_approval=true; $approvedStatus="";
							 
							
							
							
						
								
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";										
								$z=0; 
								foreach($signatory_main as $user_id=>$val)
								{
									
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
										<td width="80" rowspan="<? echo $rowspan; ?>" align="center">
											<? echo $row['wo_number']; ?>
                                        </td>
									
										<td width="80" rowspan="<? echo $rowspan; ?>"><p><? echo $supplier_arr[$row['supplier_id']]; ?>&nbsp;</p></td>
										<td width="80" rowspan="<? echo $rowspan; ?>"><p><? echo change_date_format($row['wo_date']); ?>&nbsp;</p></td>
                                     

									    <?
									}
									
									$approved_no=''; $user_ip='';								
									$approval_date=$user_approval_array[$wo_id][$user_id];
									$user_ip=$user_ip_array[$wo_id][$user_id];
									if($approval_date!="") $approved_no=$approved_no_array[$wo_id][$user_id];					
									
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
										<td width="100" align="center"><p><? if($row['is_approved']!=0) echo $date; ?>&nbsp;</p></td>
										<td width="100" align="center"><p><? if($row['is_approved']!=0) echo $time; ?>&nbsp;</p></td>
										<td><p>&nbsp;<? if($row['is_approved']!=0) echo $approved_no; ?>&nbsp;</p></td>
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