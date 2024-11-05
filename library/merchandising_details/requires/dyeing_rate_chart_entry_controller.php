<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

if ($action == "check_conversion_rate") 
{
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

if ($action == "mrr_popup_info")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrrID) {

			var splitArr = mrrID.split("**");
            $("#hidden_mst_id").val(splitArr[0]); 		// id number
            $("#hidden_mrr_no").val(splitArr[1]); 		// mrr no
            parent.emailwindow.hide();
        }

		function calculate_date()
		{	
			var thisDate=($('#txt_date_from').val()).split('-');
			
			var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
			
			//alert(last);return;
			var last_date = last.getDate();
			var month = last.getMonth()+1;
			var year = last.getFullYear();
			
			if(month<10)
			{
				var months='0'+month;
			}
			else
			{
				var months=month;
			}
			
			var last_full_date=last_date+'-'+months+'-'+year;
			var first_full_date='01'+'-'+months+'-'+year;
			
			$('#txt_date_from').val(first_full_date);
			$('#txt_date_to').val(last_full_date);
		
		}
    </script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Company</th>
							<th width="150" align="center" >System ID</th>
							<th width="150">Year</th>
							<th width="200">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
								class="formbutton"/></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
									?>
								</td>
								<td width="" align="center" id="search_by_td">
									<input type="text" style="width:230px" class="text_boxes" name="txt_mrr_no"
									id="txt_mrr_no"/>
								</td>
								<td>
									<?
									$selected_year=date("Y");
									echo create_drop_down( "cbo_year", 110, $year,"", 1, "--Select Year--", $selected_year, "",0 );
									?>
								</td>
								<td align="center">
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
									placeholder="From Date" onchange="calculate_date()"/>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
									placeholder="To Date"/>
								</td>
								<td align="center">
									<input type="button" name="btn_show" class="formbutton" value="Show"
									onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_mrr_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_mrr_search_list_view', 'search_div', 'dyeing_rate_chart_entry_controller', 'setFilterGrid(\'list_view\',-1)')"
									style="width:100px;"/>
								</td>
							</tr>
							<tr>
								<td align="center" height="40" valign="middle" colspan="5">
									<? echo load_month_buttons(1); ?>
									<!-- Hidden field here -->
									<input type="hidden" id="hidden_mst_id" value=""/>
									<input type="hidden" id="hidden_mrr_no" value=""/>
									<!--END -->
								</td>
							</tr>
						</tbody>
					</tr>
				</table>
				<div align="center" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit;
}

if ($action == "create_mrr_search_list_view") 
{
	$ex_data = explode("_", $data);
	$company = $ex_data[0];
	$cbo_year = $ex_data[1];
	$txt_mrr_no = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$sql_cond = "";
	//var_dump($fromDate);die;

	if($company==0)
	{
		echo "<span style='color:red; font-size:18px;'>Please Select First Company Name....</span>";
		return;
	}

	if($fromDate=='' || $toDate=='')
	{
		echo "<span style='color:red; font-size:18px;'>Please Select From date and To date....</span>";
		return;
	}
	

	if ($company != "" && $company * 1 != 0) $sql_cond .= " and a.company_id='$company'";
	if ($txt_mrr_no != "" ) $sql_cond .= " and a.dyeing_rate_no like '%$txt_mrr_no%'";

	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$sql_cond .= " and b.from_date between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and b.from_date between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}

	
	if($db_type==0)
	{
		if($cbo_year!=0) $sql_cond .=" and year(b.from_date)=$cbo_year"; //else $sql_cond="";
	}
	elseif($db_type==2)
	{
		if($cbo_year!=0) $sql_cond .=" and to_char(b.from_date,'yyyy')=$cbo_year"; //else $year_cond="";
	}

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	$sql="SELECT A.ID, A.COMPANY_ID, A.DYEING_RATE_NO, B.FROM_DATE, B.TO_DATE FROM LIB_DYEING_RATE_CHART_MST A, LIB_DYEING_RATE_CHART_DTLS B WHERE A.ID=B.MST_ID $sql_cond AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.ENTRY_FORM=540 GROUP BY A.ID, A.COMPANY_ID, A.DYEING_RATE_NO, B.FROM_DATE, B.TO_DATE ORDER BY A.ID DESC";

	//echo $sql;
	$result = sql_select($sql);
	?>
	<div style="margin-top:5px">
		<div style="width:600px;">
			<table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="150">Company</th>
					<th width="100">System No</th>
					<th width="150">From Date</th>
					<th width="150">To Date</th>
				</thead>
			</table>
		</div>
		<div style="width:600px;overflow-y:scroll; max-height:210px;" id="search_div">
			<table cellspacing="0" cellpadding="0" width="580" class="rpt_table" id="list_view" border="1" rules="all">
				<?php
				$i = 1;
				foreach ($result as $row) 
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onclick="js_set_value('<? echo $row['ID'] . '**' . $row['DYEING_RATE_NO']; ?>')">
						<td width="30"><?php echo $i; ?></td>
						<td width="150"><p><?php echo $company_arr[$row["COMPANY_ID"]]; ?></p></td>
						<td width="100"><p><?php echo $row["DYEING_RATE_NO"]; ?></p></td>
						<td width="150"><p><?php echo change_date_format($row["FROM_DATE"]); ?></p></td>
						<td width="150"><p><?php echo change_date_format($row["TO_DATE"]); ?></p></td>
						
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                </table>
            </div>
        </div>
        <?
        exit();
}

if ($action=="search_list_view")
{
	$lib_com_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name");
	
	$sql="SELECT id, mst_id, company_id, fabric_type_id, color_range_id, exchange_rate, rate, from_date, to_date, currency_id from  lib_dyeing_rate_chart_dtls where is_deleted=0 and entry_form=540 order by from_date DESC";

	$arr=array (0=>$lib_com_arr,1=>$fabric_type_for_dyeing, 2=>$color_range,3=>$currency);
	echo  create_list_view ( "list_view", "Production Company Name,Fabric Type,Color Range,Currency,Exchange Rate,Rate,From Date,To Date", "200,100,100,100,100,100,100,100","1000","350",0, $sql, "get_php_form_data", "mst_id", "'load_php_data_to_form'",1, "company_id,fabric_type_id,color_range_id,currency_id,0,0,0,0", $arr , "company_id,fabric_type_id,color_range_id,currency_id,exchange_rate,rate,from_date,to_date", "requires/dyeing_rate_chart_entry_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,3,3') ;

	exit();
}
    
if ($action=="load_php_data_to_form")
{
	
	$sql="SELECT id, dyeing_rate_no from lib_dyeing_rate_chart_mst where is_deleted=0 and entry_form=540 and id='$data' order by id DESC";

	//echo $sql;die;
	$nameArray=sql_select($sql);
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_dyeing_rate').value  = '".($inf[csf("dyeing_rate_no")])."';\n";
		echo "document.getElementById('update_mst_id').value  = '".($inf[csf("id")])."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_dyeing_rate_chart_entry',1);\n";  
		echo "show_detail_form('".$inf[csf("id")]."');\n"; 
		echo "enable_disable();\n"; 
	}
	exit();
}

if($action =="show_detail_form")
{
	?>
	<table width="100%" border="0" id="tbl_dyeing_rate_chart" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <tr>
				<th width="150" class="must_entry_caption">Production Company Name</th>
				<th width="150">Fabric Type</th>
				<th width="150">Color Range</th>
				<th width="120">Currency</th>
                <th width="50">Exchange Rate</th>
				<th width="70">Rate</th>
				<th width="120">From Date</th>
				<th width="120">To Date</th>
				<th>&nbsp;</th> 
            </tr>
        </thead>
        <tbody>
			<?
				$data_array=sql_select("SELECT id, mst_id, company_id, fabric_type_id, color_range_id, exchange_rate, rate,from_date, to_date, currency_id from  lib_dyeing_rate_chart_dtls where mst_id='$data' and is_deleted=0 and entry_form=540 order by id DESC");

				if(count($data_array)>0)
				{
					$i=0;
					$lib_yarn_count=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");

					foreach($data_array as $row)
					{
						$txt_count=$lib_yarn_count[$row[csf('count_id')]];
						if(change_date_format($row[csf("from_date")]) != change_date_format(date('Y-m-01')))
						{
							$disabled = 'disabled';
							$disabled_drop = 1;
						}
						else
						{
							$disabled = '';
							$disabled_drop = '';
							
						}
						
						$i++;
						?>
						<tr id="yarncost_<?=$i; ?>" align="center">
							<td>
								<?=create_drop_down( "cbocompanyid_".$i, 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $row[csf("company_id")], "check_duplicate(".$i.",this.id);opencurreny(".$i.",this.id);",$disabled_drop );
								
								?>

							</td>
							<td>
								<?=create_drop_down( "cbofabrictype_".$i, 150, $fabric_type_for_dyeing,"", 1, "-- Select --", $row[csf("fabric_type_id")], 'check_duplicate(1,this.id)',$disabled_drop,'','','','' ); 
								?>
                                   
							</td>
							<td><?=create_drop_down( "cbocolorrange_".$i, 150, $color_range,"", 1, "-- Select --", $row[csf("color_range_id")], '',$disabled_drop,'','','','' ); ?></td>
							<td>
								<?
								echo create_drop_down( "cbocurrency_".$i, 120, $currency,"", 1, "-- Select--", $row[csf("currency_id")], "exchange_rate(".$i.",this.value)", $disabled_drop );
								?>
							</td>
							<td><input type="text" id="txtcurrency_<?=$i; ?>",  name="txtcurrency_<?=$i; ?>" class="text_boxes_numeric" style="width:50px" value="<?=$row[csf("exchange_rate")]; ?>" disabled/></td>

							<td><input type="text" id="txtrate_<?=$i; ?>"  name="txtrate_<?=$i; ?>"  class="text_boxes_numeric" style="width:70px" value="<?=$row[csf("rate")]; ?>" <?=$disabled;?>/></td>
							<td>
								<input type="text" id="txtformdate_<?=$i; ?>"  name="txtformdate_<?=$i; ?>"  class="datepicker" style="width:80px" onchange='calculate_date(<?=$i; ?>)' value="<?=change_date_format($row[csf("from_date")]); ?>"  <?=$disabled;?> />

								<input type="hidden" id="txtformdate_<?=$i; ?>"  name="txtformdate_<?=$i; ?>"  class="datepicker" style="width:80px" value="<? echo $date_cond; ?>" />

							</td>
							<td>
								<input type="text" id="txttodate_<?=$i; ?>"  name="txttodate_<?=$i; ?>"  class="datepicker" style="width:80px" value="<?=change_date_format($row[csf("to_date")]); ?>" disabled/>
							
							</td>
                            
                            <td> 
                                <input type="button" id="increase_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
                                <input type="button" id="decrease_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?>);" />
								<input type="hidden" id="updateid_<?=$i; ?>"  name="updateid_<?=$i; ?>"  class="text_boxes_numeric" style="width:70px" value="<?=$row[csf("id")]; ?>" />
                            </td>  
						</tr>
						<?
					}
				}
				else
				{
					?>
                        <tr id="dyeingcost_1" align="center">
							<td>
								<?=create_drop_down( "cbocompanyid_1", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "check_duplicate(1,this.id);opencurreny(1,this.id);" );
								?>
                                </td>
                                <td> <?=create_drop_down( "cbofabrictype_1", 150, $fabric_type_for_dyeing,"", 1, "-- Select --", '', 'check_duplicate(1,this.id)','','','','','' ); ?>
                                   
                                </td>
                                <td><?=create_drop_down( "cbocolorrange_1", 150, $color_range,"", 1, "-- Select --", '', '','','','','','' ); ?></td>

								<td>
									<?
									echo create_drop_down( "cbocurrency_1", 120, $currency,"", 1, "-- Select--", 0, "exchange_rate(1,this.value)",'' );
									?>
                                </td>

                                <td><input type="text" id="txtcurrency_1"  name="txtcurrency_1" class="text_boxes_numeric" style="width:50px" value="" disabled/></td>

                                 <td><input type="text" id="txtrate_1"  name="txtrate_1"  class="text_boxes_numeric" style="width:70px" value="" /></td>
								 <td>
                                 	<input type="text" id="txtformdate_1"  name="txtformdate_1"  class="datepicker" style="width:80px" onchange="calculate_date(1)" readonly />
                                 </td>
                                 <td>
                                 	<input type="text" id="txttodate_1"  name="txttodate_1"  class="datepicker" style="width:80px" disabled/>
                                 </td>
                            <td> 
                                <input type="button" id="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decrease_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1);" />
								<input type="hidden" id="updateid_1"  name="updateid_1"  class="text_boxes_numeric" style="width:70px" value="" />
                            </td>  
                        </tr>
					<?
				}
	            ?>
        </tbody>
	</table>
	
	<?
	exit();
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompanyid="cbocompanyid_".$i;
			$cbofabrictype="cbofabrictype_".$i;
			$cbocolorrange="cbocolorrange_".$i;
			$txtformdate="txtformdate_".$i;
			$txttodate="txttodate_".$i;

			//company_id='".str_replace("'", "", $$cbocompanyid)."' and 

			$duplicate = is_duplicate_field("id"," lib_dyeing_rate_chart_dtls","company_id='".str_replace("'", "", $$cbocompanyid)."' and fabric_type_id='".str_replace("'", "", $$cbofabrictype)."' and color_range_id='".str_replace("'", "", $$cbocolorrange)."' and from_date=".$$txtformdate."  and to_date=".$$txttodate."  and entry_form=540 and status_active=1 and is_deleted=0 "); 
			if($duplicate==1) 
			{			 
				echo "20**Duplicate Fabric Type :".$fabric_type_for_dyeing[str_replace("'", "", $$cbofabrictype)]." and Color Range :".$color_range[str_replace("'", "", $$cbocolorrange)]." Found Within ".$company_library[str_replace("'", "", $$cbocompanyid)];
				die;
			}
		}
		
		$id=return_next_id("id", "lib_dyeing_rate_chart_mst", 1);

        if($db_type==0) $insert_date_con="YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

        $new_dyeing_rate_number=explode("*",return_mrr_number( str_replace("'","",$hdn_company_id), '', 'DRC', date("Y",time()), 5, "SELECT dyeing_rate_no_prefix, dyeing_rate_prefix_num from lib_dyeing_rate_chart_mst where company_id=$hdn_company_id and $insert_date_con and entry_form = 540 order by id desc", "dyeing_rate_no_prefix", "dyeing_rate_prefix_num" ));
        //echo "10**".$new_dyeing_rate_number[0];die;


		$field_array_mst= "id,dyeing_rate_no,dyeing_rate_no_prefix,dyeing_rate_prefix_num,company_id,entry_form,status_active,is_deleted,inserted_by,insert_date";

		$data_array_mst="(".$id.",'".$new_dyeing_rate_number[0]."','".$new_dyeing_rate_number[1]."','".$new_dyeing_rate_number[2]."',".$hdn_company_id.",540,1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$id_dtls=return_next_id( "id", "lib_dyeing_rate_chart_dtls", 1 ) ;

		$field_array_dtls= "id,mst_id, company_id, fabric_type_id, color_range_id, currency_id, exchange_rate, rate,from_date,to_date,entry_form,status_active,is_deleted, inserted_by, insert_date";

		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompanyid="cbocompanyid_".$i;
			$cbofabrictype="cbofabrictype_".$i;
			$cbocolorrange="cbocolorrange_".$i;
			$txtcurrency="txtcurrency_".$i;
			$txtrate="txtrate_".$i;
			$txtformdate="txtformdate_".$i;
			$txttodate="txttodate_".$i;
			$cbocurrency="cbocurrency_".$i;
			
			if ($i!=1) $data_array_dtls .=",";

			$data_array_dtls .="(".$id_dtls.",".$id.",'".str_replace("'", "", $$cbocompanyid)."','".str_replace("'", "", $$cbofabrictype)."','".str_replace("'", "", $$cbocolorrange)."','".str_replace("'", "", $$cbocurrency)."',".$$txtcurrency.",".$$txtrate.",".$$txtformdate.",".$$txttodate.",540,1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_dtls=$id_dtls+1;
		}
		
		//echo "10**INSERT INTO lib_dyeing_rate_chart_mst(".$field_array_mst.") VALUES ".$data_array_mst;die;
		//echo "10**INSERT INTO lib_dyeing_rate_chart_dtls(".$field_array_dtls.") VALUES ".$data_array_dtls;die;
		
		
		$rID=sql_insert("lib_dyeing_rate_chart_mst",$field_array_mst,$data_array_mst,0);
		$rID_1=sql_insert("lib_dyeing_rate_chart_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "10**".$rID."**".$rID_1; die;
		if($db_type==0)
		{
			if($rID && $rID_1){
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**INSERT INTO lib_dyeing_rate_chart_mst(".$field_array_mst.") VALUES ".$data_array_mst;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_1)
			{
				oci_commit($con);  
				echo "0**".$rID;
			}
		else{
				oci_rollback($con); 
				echo "10**INSERT INTO lib_dyeing_rate_chart_mst(".$field_array_mst.") VALUES ".$data_array_mst;
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

		
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompanyid="cbocompanyid_".$i;
			$cbofabrictype="cbofabrictype_".$i;
			$cbocolorrange="cbocolorrange_".$i;
			$txtformdate="txtformdate_".$i;
			$txttodate="txttodate_".$i;
			$updateIdDtls="updateid_".$i;
			//echo "20**".$$updateid;die;

			//---------------Check Duplicate  ------------------------//
			
		
			if(str_replace("'", "", $$updateIdDtls) !='')
			{
				
				if ($db_type == 0) 
				{
					$date_cond = "and from_date = " . $$txtformdate . " and to_date =" . $$txttodate . "";
				} 
				else 
				{
					$date_cond = "and from_date = " . $$txtformdate. " and to_date =" . $$txttodate. "";
				}
			
				// 

				$duplicate = is_duplicate_field("id"," lib_dyeing_rate_chart_dtls","company_id='".str_replace("'", "", $$cbocompanyid)."' and fabric_type_id='".str_replace("'", "", $$cbofabrictype)."' and color_range_id='".str_replace("'", "", $$cbocolorrange)."'$date_cond and id<>".str_replace("'", "", $$updateIdDtls)." and entry_form=540 and status_active=1 and is_deleted=0 "); 
				if($duplicate==1) 
				{			 
					echo "20**Duplicate Fabric Type :".$fabric_type_for_dyeing[str_replace("'", "", $$cbofabrictype)]." and Color Range :".$color_range[str_replace("'", "", $$cbocolorrange)]." Found Within ".$company_library[str_replace("'", "", $$cbocompanyid)];
					die;
				}
			}
			else
			{
				$duplicate = is_duplicate_field("id"," lib_dyeing_rate_chart_dtls","company_id='".str_replace("'", "", $$cbocompanyid)."' and fabric_type_id='".str_replace("'", "", $$cbofabrictype)."' and color_range_id='".str_replace("'", "", $$cbocolorrange)."' $date_cond and entry_form=540 and status_active=1 and is_deleted=0 "); 
				if($duplicate==1) 
				{			 
					echo "20**Duplicate Fabric Type :".$fabric_type_for_dyeing[str_replace("'", "", $$cbofabrictype)]." and Color Range :".$color_range[str_replace("'", "", $$cbocolorrange)]." Found Within ".$company_library[str_replace("'", "", $$cbocompanyid)];
					die;
				}
			}
			
		
			
		
		}
		
		$field_array_mst="updated_by*update_date";
		$data_array_mst="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID_de1=execute_query( "delete from lib_dyeing_rate_chart_dtls where  mst_id =".$update_mst_id."",0);

		
		$id_dtls=return_next_id( "id", "lib_dyeing_rate_chart_dtls", 1 ) ;
		
		$field_array_dtls= "id,mst_id, company_id, fabric_type_id, color_range_id, currency_id, exchange_rate, rate, from_date,to_date,entry_form,status_active,is_deleted, inserted_by, insert_date";
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$cbocompanyid="cbocompanyid_".$i;
			$cbofabrictype="cbofabrictype_".$i;
			$cbocolorrange="cbocolorrange_".$i;
			$txtcurrency="txtcurrency_".$i;
			$txtrate="txtrate_".$i;
			$txtformdate="txtformdate_".$i;
			$txttodate="txttodate_".$i;
			$cbocurrency="cbocurrency_".$i;
			
			if ($i!=1) $data_array_dtls .=",";

			$data_array_dtls .="(".$id_dtls.",".$update_mst_id.",'".str_replace("'", "", $$cbocompanyid)."','".str_replace("'", "", $$cbofabrictype)."','".str_replace("'", "", $$cbocolorrange)."','".str_replace("'", "", $$cbocurrency)."',".$$txtcurrency.",".$$txtrate.",".$$txtformdate.",".$$txttodate.",540,1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_dtls=$id_dtls+1;
		}

		$rID=sql_update("lib_dyeing_rate_chart_mst",$field_array_mst,$data_array_mst,"id","".$update_mst_id."",0);
		$rID_1=sql_insert("lib_dyeing_rate_chart_dtls",$field_array_dtls,$data_array_dtls,1);
		
		
		//echo "10**".$rID_de1."**".$rID."**".$rID_1; die;
		if($db_type==0)
		{
			if($rID && $rID_1 && $rID_de1){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID_1 && $rID_de1)
			{
				oci_commit($con);  
				echo "1**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
		
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array_mst="updated_by*update_date*status_active*is_deleted";
		$data_array_mst="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_dyeing_rate_chart_mst",$field_array_mst,$data_array_mst,"id","".$update_mst_id."",1);
		$field_array_dtls="updated_by*update_date*status_active*is_deleted";
		$data_array_dtls="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID1=sql_delete("lib_dyeing_rate_chart_dtls",$field_array_dtls,$data_array_dtls,"mst_id","".$update_mst_id."",1);
		
		if($db_type==0)
		{
			if($rID && $rID1 ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		 if($rID && $rID1 )
			{
				oci_commit($con);   
				echo "2**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}

?>