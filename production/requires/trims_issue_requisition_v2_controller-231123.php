<?
session_start();
include('../../includes/common.php');
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//************************************ Start *************************************************


if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 152, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected,"load_drop_down( 'requires/trims_issue_requisition_v2_controller', this.value+'__'+document.getElementById('cbo_working_company').value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/trims_issue_requisition_v2_controller', this.value+'__'+document.getElementById('cbo_working_company').value, 'load_drop_down_store', 'store_id' );" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data_ref=explode("__",$data);
	$location_id=$data_ref[0];
	$company_id=$data_ref[1];
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and a.location_id=$location_id and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "" );
	exit();
}


if ($action=="load_drop_down_floor")
{
	$data_ref=explode("__",$data);
	$location_id=$data_ref[0];
	$company_id=$data_ref[1];
	echo create_drop_down( "cbo_floor_name", 152, "SELECT id,floor_name from lib_prod_floor where company_id=$company_id and location_id=$location_id and production_process=5 and status_active=1 and is_deleted=0 ","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/trims_issue_requisition_v2_controller', this.value+'__'+document.getElementById('cbo_working_company').value+'__'+document.getElementById('cbo_working_location').value, 'load_drop_down_sewing_line', 'sewing_td' );" );
	exit();
}
if($action=="load_drop_down_sewing_line")
{
	$explode_data = explode("__",$data);
	$location = $explode_data[2];
	$company = $explode_data[1];
	$floor = $explode_data[0];

	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$company' and a.location_id='$location' and a.floor_id='$floor' and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number");
	//echo $line_data;die;

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
	echo create_drop_down( "cbo_sewing_line", 152,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
}

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo date("Y");die;
	?> 

	<script>
		 function js_set_value(job_no, color_id) {
            $("#hidden_job_no").val(job_no);
            $("#hidden_color_id").val(color_id);
            parent.emailwindow.hide();
        }
	
    </script>

	</head>

	<body onLoad="set_hotkey()">
	<div align="center" style="width:990px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:990px;">
			<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="950" class="rpt_table">
					<thead>
						<th width="160">Buyer</th>
						<th width="150">Job Year</th>
						<th width="160">Search By</th>
						<th width="160">Search</th>
                        <th width="180"><? if($cbo_trim_type==1) echo "Cutting Date Range"; else echo "Input Date Range"; ?></th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
							<input type="hidden" name="hidden_color_id" id="hidden_color_id" value=""> 
						</th> 
					</thead>
					<tr class="general">
						<td align="center">	
							<?
							echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
							?> 
						</td>
						<td align="center"><? echo create_drop_down( "cbo_job_year", 120, $year,"", 1, "-- Select Year --", date("Y"), "" );?></td>
						<td align="center">	
							<?
								if($cbo_trim_type==1)
								{
									$search_by_arr=array(2=>"Job No",3=>"Style Ref.",5=>"Cutting No.");
								}
								else
								{
									$search_by_arr=array(2=>"Job No",3=>"Style Ref.",5=>"Sewing No.");
								}
								
								echo create_drop_down( "cbo_search_by", 140, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>                 
						<td align="center">				
							<input type="text" style="width:120px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" />&nbsp;To&nbsp;
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" />
                        </td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id ?>+'_'+<? echo $cbo_trim_type ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_po_search_list_view', 'search_div', 'trims_issue_requisition_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
                    <tr>                  
                        <td align="center" height="40" valign="middle" colspan="6"> <? echo load_month_buttons(1);  ?></td>
                    </tr> 
			</table>
				<div style="margin-top:10px;" id="search_div" align="left"></div> 
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data = explode("_",$data);
	$cbo_buyer_name=trim(str_replace("'","",$data[0]));
	$cbo_job_year=trim(str_replace("'","",$data[1]));
	$cbo_search_by =trim(str_replace("'","",$data[2]));
	$txt_search_common=trim(str_replace("'","",$data[3]));
	$cbo_company_id =trim(str_replace("'","",$data[4]));
	$cbo_trim_type =trim(str_replace("'","",$data[5]));
	
	$date_form =trim(str_replace("'","",$data[6]));
	$date_to =trim(str_replace("'","",$data[7]));
	
	if($cbo_buyer_name==0 && $txt_search_common=="") 
	{
		if( $date_form=="" && $date_to=="")
		{
			echo "Please Select Specific Reference.";
			die;
		}
	}
	$sql_cond="";
	if($cbo_buyer_name>0) $sql_cond.=" and a.BUYER_NAME=$cbo_buyer_name";
	//if($cbo_trim_type>0) $sql_cond.=" and e.trim_type=$cbo_trim_type ";
	if($txt_search_common!="")
	{
		if($cbo_search_by==1) $sql_cond.=" and b.PO_NUMBER='$txt_search_common'";
		else if($cbo_search_by==2) $sql_cond.=" and a.JOB_NO like '%$txt_search_common'";
		else if($cbo_search_by==3) $sql_cond.=" and a.STYLE_REF_NO ='$txt_search_common'";
		else if($cbo_search_by==4) $sql_cond.=" and b.GROUPING ='$txt_search_common'";
		else
		{
			if($cbo_trim_type==1)
			{
				$sql_cond.=" and d.CUTTING_NO like '%$txt_search_common'";
			}
			else
			{
				$sql_cond.=" and g.SYS_NUMBER like '%$txt_search_common'";
			}
		}
	}
	
	if($cbo_job_year>0)
	{
		if($db_type==0) $sql_cond.=" and year(a.insert_date)='$cbo_job_year'";
		else $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
	}
	
	if($date_form!="" && $date_to!="") $sql_cond.=" and d.ENTRY_DATE between '" . change_date_format($date_form, '', '', 1) . "' and '" . change_date_format($date_to, '', '', 1) . "'";
	
	
	//echo $po_sql;//die;
	$buyer_arr = return_library_array("SELECT buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.short_name",'id','short_name');
	$company_arr = return_library_array("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	//$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where item_category=4",'id','item_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	//$conversion_arr = return_library_array("SELECT a.id, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0",'id','conversion_factor' ); 

	
	if($cbo_trim_type==1)
	{
		$po_sql="SELECT a.ID as JOB_ID, a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, d.COMPANY_ID, d.CUTTING_NO as CUT_SEWING_NO, d.ENTRY_DATE as PLAN_DATE, e.ORDER_CUT_NO, e.COLOR_ID
		from wo_po_details_master a, wo_po_break_down b, PPL_CUT_LAY_MST d, ppl_cut_lay_dtls e
		where a.id=b.job_id and b.JOB_NO_MST=d.JOB_NO and d.id=e.mst_id and b.shiping_status!=3 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.COMPANY_NAME=$cbo_company_id $sql_cond
		group by a.ID, a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, d.COMPANY_ID, d.CUTTING_NO, d.ENTRY_DATE, e.ORDER_CUT_NO, e.COLOR_ID";
		$result = sql_select($po_sql);
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="120">Company Name</th>
                <th width="100">Buyer</th>
                <th width="120">Job No</th>			
                <th width="150">Style Ref. No.</th>
                <th width="80">Cutting Date</th>
                <th width="120">System Cut No</th>               
                <th width="100">Order Cut No</th>
                <th>Color</th>
            </thead>
        </table>
        <div style="width:1010px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table" id="tbl_list_search">  
            <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                if(in_array($selectResult[csf('id')],$hidden_po_id)) 
                {
                    if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  id="search<? echo $i;?>" onClick="js_set_value('<? echo $row['JOB_NO']; ?>','<? echo $row['COLOR_ID']; ?>');"> 
                    <td width="30" align="center"><? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['PO_ID']; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['ITEM_GROUP_ID']; ?>"/>
                    </td>
                    <td width="120" title="<?= $row['COMPANY_ID'];?>"><p><? echo $company_arr[$row['COMPANY_ID']]; ?>&nbsp;</p></td>
                    <td width="100" title="<?= $row['BUYER_NAME'];?>"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row['JOB_NO']; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $row['STYLE_REF_NO']; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row['PLAN_DATE']) ?>&nbsp;</p></td>
                    <td width="120"><? echo $row['CUT_SEWING_NO']; ?></td>               
                    <td width="100" align="center"><p><? echo $row['ORDER_CUT_NO'];  ?>&nbsp;</p></td>
                    <td><p><? echo $color_arr[$row['COLOR_ID']]; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
        <?
	}
	else
	{
		$floor_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5",'id','floor_name');
		$nameArray = sql_select("select id, auto_update from variable_settings_production where company_name=$cbo_company_id and variable_list=23 and status_active=1 and is_deleted=0");
		$prod_reso_allocation = $nameArray[0][csf('auto_update')];
		//echo $prod_reso_allocation.test;die;
		if ($prod_reso_allocation == 1) 
		{
			$line_library = return_library_array("select id,line_name,sewing_line_serial from lib_sewing_line where status_active=1 order by sewing_line_serial", "id", "line_name");
			$line_array = array();
	
			$line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
	
	
			$line_merge=9999;
			foreach($line_data as $row)
			{
				$line='';
				$line_number=explode(",",$row[csf('line_number')]);
				foreach($line_number as $val)
				{
					if(count($line_number)>1)
					{
						$line_merge++;
						$new_arr[$line_merge]=$row[csf('id')];
					}
					else
					{
						if($new_arr[$line_library[$val]])
						$new_arr[$line_library[$val]." "]=$row[csf('id')];
						else
							$new_arr[$line_library[$val]]=$row[csf('id')];
					}
	
					if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
				}
				$line_array[$row[csf('id')]]=$line;
			}
			//ksort($new_arr);
			foreach($new_arr as $key=>$v)
			{
				$line_array_new[$v]=$line_array[$v];
			}
	
		} 
		else 
		{
			$line_array_new = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1",'id','line_name');
		}
		
		
		
		$po_sql="SELECT a.ID as JOB_ID, a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, d.COMPANY_ID, d.CUTTING_NO as CUT_SEWING_NO, d.ENTRY_DATE as PLAN_DATE, e.ORDER_CUT_NO, e.COLOR_ID, g.SYS_NUMBER, g.FLOOR_ID, g.SEWING_LINE, g.DELIVERY_DATE
		from wo_po_details_master a, wo_po_break_down b, PPL_CUT_LAY_MST d, ppl_cut_lay_dtls e, PRO_GARMENTS_PRODUCTION_DTLS f, pro_gmts_delivery_mst g
		where a.id=b.job_id and b.JOB_NO_MST=d.JOB_NO and d.id=e.mst_id and d.CUTTING_NO=f.CUT_NO and f.DELIVERY_MST_ID=g.id and f.PRODUCTION_TYPE=4 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.COMPANY_NAME=$cbo_company_id $sql_cond
		group by a.ID, a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, d.COMPANY_ID, d.CUTTING_NO, d.ENTRY_DATE, e.ORDER_CUT_NO, e.COLOR_ID, g.SYS_NUMBER, g.FLOOR_ID, g.SEWING_LINE, g.DELIVERY_DATE";
		$result = sql_select($po_sql);
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="120">Company Name</th>
                <th width="80">Buyer</th>
                <th width="100">Job No</th>			
                <th width="110">Style Ref. No.</th>
                <th width="70">Input Date</th>
                <th width="100">System Cut No</th>               
                <th width="100">Challan No</th>
                <th width="100">Color</th>
                <th width="100">Floor</th>
                <th>Line</th>
            </thead>
        </table>
        <div style="width:1040px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table" id="tbl_list_search">  
            <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                if(in_array($selectResult[csf('id')],$hidden_po_id)) 
                {
                    if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  id="search<? echo $i;?>" onClick="js_set_value('<? echo $row['JOB_NO']; ?>','<? echo $row['COLOR_ID']; ?>');"> 
                    <td width="30" align="center"><? echo $i; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['PO_ID']; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['ITEM_GROUP_ID']; ?>"/>
                    </td>
                    <td width="120" title="<?= $row['COMPANY_ID'];?>"><p><? echo $company_arr[$row['COMPANY_ID']]; ?>&nbsp;</p></td>
                    <td width="80" title="<?= $row['BUYER_NAME'];?>"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $row['JOB_NO']; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $row['STYLE_REF_NO']; ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row['DELIVERY_DATE']) ?>&nbsp;</p></td>
                    <td width="100"><? echo $row['CUT_SEWING_NO']; ?></td>               
                    <td width="100" align="center"><p><? echo $row['SYS_NUMBER'];  ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $color_arr[$row['COLOR_ID']]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $floor_arr[$row['FLOOR_ID']]; ?>&nbsp;</p></td>
                    <td><p><? echo $line_array_new[$row['SEWING_LINE']]; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
        <?
	}
	?>
	
    <table width="990" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%"> 
                    <div style="width:50%; float:left" align="left">&nbsp;
                       <!-- <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All-->
                    </div>
                    <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
	<?
	exit();
}


if($action=="job_wise_po_data")
{
	$nameArray= sql_select("select ID from wo_po_break_down where JOB_NO_MST='$data' and status_active=1 and is_deleted=0");
	if(count($nameArray)>0)
	{
		$all_po_id="";
		foreach($nameArray as $val)
		{
			$all_po_id.=$val["ID"].",";
		}
		echo chop($all_po_id,",");
	}
	else
	{
		echo "0";
	}
	die;
}


if($action=="cut_item_details")
{
	$data_ref=explode("__",$data);
	$item_job_no=$data_ref[0];
	$gmst_color_id=$data_ref[1];
	$cbo_trim_type=$data_ref[2];
	$cbo_company_id=$data_ref[3];
	$generate_level=$data_ref[4];
	//echo $cbo_trim_type;die;
	$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_library=return_library_array( "select id, country_name from lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	
	//echo $cut_size_sql;die;
	if($cbo_trim_type==1)
	{
		$cut_size_sql="select a.CUTTING_NO, a.JOB_NO, b.COLOR_ID, c.ORDER_ID, c.SIZE_ID, c.MARKER_QTY 
		from PPL_CUT_LAY_MST a, PPL_CUT_LAY_DTLS b, PPL_CUT_LAY_SIZE c 
		where a.id=b.mst_id and b.id=c.DTLS_ID and a.id=b.mst_id and a.JOB_NO='$item_job_no' and b.COLOR_ID=$gmst_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $cut_size_sql;
		$cut_size_sql_result = sql_select($cut_size_sql);
		$size_data_arr=array();$size_wise_qnty_array=array();
		foreach($cut_size_sql_result as $val)
		{
			$size_data_arr[$val["SIZE_ID"]]=$size_arr[$val["SIZE_ID"]];
			$size_wise_qnty_array[$val["CUTTING_NO"]][$val["ORDER_ID"]][$val["COLOR_ID"]][$val["SIZE_ID"]]+=$val["MARKER_QTY"];
		}
		
		$po_sql="SELECT d.COMPANY_ID, d.CUTTING_NO as CUT_SEWING_NO, d.ENTRY_DATE as PLAN_DATE, e.ORDER_CUT_NO, a.ID as JOB_ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, b.ID as PO_ID, b.PO_NUMBER, e.COLOR_ID, e.ROLL_DATA
		from wo_po_details_master a, wo_po_break_down b, PPL_CUT_LAY_MST d, ppl_cut_lay_dtls e, PPL_CUT_LAY_SIZE f
		where a.id=b.job_id and b.JOB_NO_MST=d.JOB_NO and d.id=e.mst_id and e.ID=f.DTLS_ID and b.id=f.ORDER_ID and b.shiping_status!=3 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.JOB_NO='$item_job_no' and e.COLOR_ID=$gmst_color_id
		group by d.COMPANY_ID, d.CUTTING_NO, d.ENTRY_DATE, e.ORDER_CUT_NO, a.ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, b.ID, b.PO_NUMBER, e.COLOR_ID, e.ROLL_DATA";
		//echo $po_sql;die;
		$po_sql_result = sql_select($po_sql);
		?>
		<thead>
			<tr>
				<th width="150">Company</th>
				<th width="90">System Cut No</th>
				<th width="80">Order Cut No.</th>
				<th width="80">Buyer</th>
				<th width="100">Style</th>
				<th width="100">Job</th>
				<th width="120">Gmts. Item</th>
				<th width="100">Country</th>
				<th width="100">PO</th>
				<th width="100">Color</th>
				<th width="150">Batch No.</th>
				<?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th width="80"><?= $size_val; ?></th>
					<?
				}
				?>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;$size_wise_total=array();
		foreach($po_sql_result as $row)
		{
			$roll_data_arr=explode("**",$row['ROLL_DATA']);
			$batch_no_arr=array();
			foreach($roll_data_arr as $roll_datas)
			{
				$roll_datas_all=explode("=",$roll_datas);
				if($roll_datas_all[5]!="") $batch_no_arr[$roll_datas_all[5]]=$roll_datas_all[5];
			}
			
			if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="fn_item_details('<? echo $row['PO_ID']; ?>','<? echo $gmst_color_id; ?>','0','0');"> 
				<td><? echo $company_arr[$row['COMPANY_ID']]; ?></td>
				<td><? echo $row['CUT_SEWING_NO']; ?></td>
				<td><? echo $row['ORDER_CUT_NO']; ?></td>
				<td><? echo $buyer_library[$row['BUYER_NAME']]; ?></td>
				<td><? echo $row['STYLE_REF_NO']; ?></td>
				<td><? echo $row['JOB_NO'] ?></td>
				<td><? echo $garments_item[$row['GMT_ITEM_ID']]; ?></td>
				<td><? echo $country_library[$row['COUNTRY_ID']]; ?></td>
				<td><? echo $row['PO_NUMBER'] ?></td>
				<td><? echo $color_arr[$row['COLOR_ID']]; ?></td>
				<td><? echo implode(",",$batch_no_arr); ?></td>
				<?
				$row_tot_size_qnty=0;
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<td align="right"><?= $size_wise_qnty_array[$row["CUT_SEWING_NO"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id]; ?></td>
					<?
					$row_tot_size_qnty+=$size_wise_qnty_array[$row["CUT_SEWING_NO"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
					$size_wise_total[$size_id]+=$size_wise_qnty_array[$row["CUT_SEWING_NO"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
				}
				?>
				<td align="right"><? echo $row_tot_size_qnty; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total:</th>
				<?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th align="right"><?= $size_wise_total[$size_id]; ?></th>
					<?
					$grand_total+=$size_wise_total[$size_id];
				}
				?>
				<th align="right"><?= $grand_total; ?></th>
			</tr>
		</tfoot>
		<?
	}
	else
	{
		
		$cut_size_sql="select c.SYS_NUMBER as CUTTING_NO, a.JOB_NO_MST as JOB_NO, a.COLOR_NUMBER_ID as COLOR_ID, a.PO_BREAK_DOWN_ID as ORDER_ID, a.SIZE_NUMBER_ID as SIZE_ID, b.PRODUCTION_QNTY as MARKER_QTY 
		from WO_PO_COLOR_SIZE_BREAKDOWN a, PRO_GARMENTS_PRODUCTION_DTLS b, PRO_GMTS_DELIVERY_MST c 
		where a.id=b.COLOR_SIZE_BREAK_DOWN_ID and b.DELIVERY_MST_ID=c.ID and a.JOB_NO_MST='$item_job_no' and a.COLOR_NUMBER_ID=$gmst_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		//echo $cut_size_sql;
		$cut_size_sql_result = sql_select($cut_size_sql);
		$size_data_arr=array();$size_wise_qnty_array=array();
		foreach($cut_size_sql_result as $val)
		{
			$size_data_arr[$val["SIZE_ID"]]=$size_arr[$val["SIZE_ID"]];
			$size_wise_qnty_array[$val["CUTTING_NO"]][$val["ORDER_ID"]][$val["COLOR_ID"]][$val["SIZE_ID"]]+=$val["MARKER_QTY"];
		}
		
		$floor_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5",'id','floor_name');
		$nameArray = sql_select("select id, auto_update from variable_settings_production where company_name=$cbo_company_id and variable_list=23 and status_active=1 and is_deleted=0");
		$prod_reso_allocation = $nameArray[0][csf('auto_update')];
		//echo $prod_reso_allocation.test;die;
		if ($prod_reso_allocation == 1) 
		{
			$line_library = return_library_array("select id,line_name,sewing_line_serial from lib_sewing_line where status_active=1 order by sewing_line_serial", "id", "line_name");
			$line_array = array();
	
			$line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
	
	
			$line_merge=9999;
			foreach($line_data as $row)
			{
				$line='';
				$line_number=explode(",",$row[csf('line_number')]);
				foreach($line_number as $val)
				{
					if(count($line_number)>1)
					{
						$line_merge++;
						$new_arr[$line_merge]=$row[csf('id')];
					}
					else
					{
						if($new_arr[$line_library[$val]])
						$new_arr[$line_library[$val]." "]=$row[csf('id')];
						else
							$new_arr[$line_library[$val]]=$row[csf('id')];
					}
	
					if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
				}
				$line_array[$row[csf('id')]]=$line;
			}
			//ksort($new_arr);
			foreach($new_arr as $key=>$v)
			{
				$line_array_new[$v]=$line_array[$v];
			}
	
		} 
		else 
		{
			$line_array_new = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1",'id','line_name');
		}
		
		$po_sql="SELECT a.COMPANY_NAME as COMPANY_ID, d.CUT_NO as CUT_SEWING_NO, a.ID as JOB_ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, c.ITEM_NUMBER_ID as GMT_ITEM_ID, b.ID as PO_ID, b.PO_NUMBER, c.COLOR_NUMBER_ID as COLOR_ID, e.SYS_NUMBER, e.FLOOR_ID, e.SEWING_LINE, e.DELIVERY_DATE
		from wo_po_details_master a, wo_po_break_down b, WO_PO_COLOR_SIZE_BREAKDOWN c, PRO_GARMENTS_PRODUCTION_DTLS d, pro_gmts_delivery_mst e
		where a.id=b.job_id and b.id=c.PO_BREAK_DOWN_ID and c.id=d.COLOR_SIZE_BREAK_DOWN_ID and d.DELIVERY_MST_ID=e.id and d.PRODUCTION_TYPE=4 and a.JOB_NO='$item_job_no' and c.COLOR_NUMBER_ID=$gmst_color_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.COMPANY_NAME, d.CUT_NO, a.ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, c.ITEM_NUMBER_ID, b.ID, b.PO_NUMBER, c.COLOR_NUMBER_ID, e.SYS_NUMBER, e.FLOOR_ID, e.SEWING_LINE, e.DELIVERY_DATE";
		//echo $po_sql;
		$po_sql_result = sql_select($po_sql);
		?>
		<thead>
			<tr>
				<th width="150">Company</th>
				<th width="90">System Cut No</th>
				<th width="90">Challan No</th>
				<th width="80">Buyer</th>
				<th width="100">Style</th>
				<th width="100">Job</th>
				<th width="120">Gmts. Item</th>
				<th width="100">PO</th>
				<th width="100">Color</th>
				<th width="100">Floor</th>
                <th width="100">Line</th>
				<?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th width="80"><?= $size_val; ?></th>
					<?
				}
				?>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;$size_wise_total=array();
		foreach($po_sql_result as $row)
		{
			if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" <? if($generate_level!=2) { ?> onClick="fn_item_details('<? echo $row['PO_ID']; ?>','<? echo $gmst_color_id; ?>','0','0');" <? } ?> > 
				<td><? echo $company_arr[$row['COMPANY_ID']]; ?></td>
				<td><? echo $row['CUT_SEWING_NO']; ?></td>
				<td><? echo $row['SYS_NUMBER']; ?></td>
				<td><? echo $buyer_library[$row['BUYER_NAME']]; ?></td>
				<td><? echo $row['STYLE_REF_NO']; ?></td>
				<td><? echo $row['JOB_NO'] ?></td>
				<td><? echo $garments_item[$row['GMT_ITEM_ID']]; ?></td>				
				<td><? echo $row['PO_NUMBER'] ?></td>
				<td><? echo $color_arr[$row['COLOR_ID']]; ?></td>
				<td><? echo $floor_arr[$row['FLOOR_ID']]; ?></td>
                <td><? echo $line_array_new[$row['SEWING_LINE']]; ?></td>
				<?
				$row_tot_size_qnty=0;
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<td align="right"><?= $size_wise_qnty_array[$row["SYS_NUMBER"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id]; ?></td>
					<?
					$row_tot_size_qnty+=$size_wise_qnty_array[$row["SYS_NUMBER"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
					$size_wise_total[$size_id]+=$size_wise_qnty_array[$row["SYS_NUMBER"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
				}
				?>
				<td align="right"><? echo $row_tot_size_qnty; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total:</th>
				<?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th align="right"><?= $size_wise_total[$size_id]; ?></th>
					<?
					$grand_total+=$size_wise_total[$size_id];
				}
				?>
				<th align="right"><?= $grand_total; ?></th>
			</tr>
		</tfoot>
		<?
	}
	
	die;
}

if($action=="duplication_check")
{
	$prev_req_data=sql_select("select c.BUYER_NAME from READY_TO_SEWING_REQSN A, WO_PO_BREAK_DOWN B , WO_PO_DETAILS_MASTER C
	where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id=$data and a.entry_form=357 and a.status_active=1 and a.is_deleted=0");
	echo $prev_req_data[0][csf("BUYER_NAME")];
}


if($action=="product_details")
{
	//echo $sqlpo; die;
	$data_ref=explode("__",$data);
	$po_id=$data_ref[0];
	$gmst_color_id=$data_ref[1];
	$variable_data_source=$data_ref[3];
	$store_id=$data_ref[4];
	
	$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, d.costing_per_id AS COSTING_PER 
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d 
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id in($po_id)";
	//echo $sqlpo; die; //and a.job_no='$job_no'
	$sqlpoRes = sql_select($sqlpo);
	//print_r($sqlpoRes); die;
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
	foreach($sqlpoRes as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$job_id=$row['JOB_ID'];
	}
	unset($sqlpoRes);
	
	$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO 
	from wo_po_details_mas_set_details a
	where a.job_id=$job_id";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
	}
	unset($gmtsitemRatioSqlRes);
	
	$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts as CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
	where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in($po_id)";
	//echo $sqlTrim; die;
	$sqlTrimRes = sql_select($sqlTrim);
	
	foreach($sqlTrimRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
		//print_r($poCountryId);
		
		if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
		{
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			
			$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
			$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
			
			$consAmt=$consQnty*$row['RATE'];
			$consTotAmt=$consTotQnty*$row['RATE'];
		}
		else
		{
			
			$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
			$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			foreach($poCountryId as $countryId)
			{
				if(in_array($countryId, $countryIdArr))
				{
					$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					$consQty=$consTotQty=0;
					
					$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
					//echo "2=".$poQty."=".$itemRatio."=".$row['CONS']."=".$costingPer;die;
					$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
					
					$consQnty+=$consQty;
					$consTotQnty+=$consTotQty;
					//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
					$consAmt+=$consQty*$row['RATE'];
					//$consTotAmt+=$consTotQty*$row['RATE'];
					$consTotAmt+=$consTotQnty*$row['RATE'];
				}
			}
		}
		//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
		$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['REQUIRED_QNTY']+=$consTotQnty;
		$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['CONS_DZN_GMTS']=$row['CONS_DZN_GMTS'];
		
	}
	//echo "<pre>";print_r($poReqDataArr);die;
	unset($sqlTrimRes);
	
	
	$prv_req_sql="SELECT a.ID as REQ_ID, b.PO_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.GMTS_COLOR_ID, b.REQSN_QTY 
	from ready_to_sewing_reqsn_mst a, ready_to_sewing_reqsn b 
	where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 and b.PO_ID in($po_id) and b.GMTS_COLOR_ID=$gmst_color_id";
	//echo $prv_req_sql;// die;
	$prv_req_result=sql_select($prv_req_sql);
	$prv_req_data=array();
	foreach($prv_req_result as $row)
	{
		$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']]+=$row['REQSN_QTY'];
	}
	//echo "<pre>";print_r($prv_req_data);die;
	
	
	$item_group_arr = $convarsion_fac_arr = array();
	$item_group_sql=sql_select("SELECT ID, ITEM_NAME, CONVERSION_FACTOR from LIB_ITEM_GROUP where item_category=4 and status_active=1 and is_deleted=0");
	foreach($item_group_sql as $row)
	{
		$item_group_arr[$row["ID"]]=$row["ITEM_NAME"];
		$convarsion_fac_arr[$row["ID"]]=$row["CONVERSION_FACTOR"];
	}
	
	if($variable_data_source==2)
	{
		$stock_sql="select b.PO_BREAKDOWN_ID, b.PROD_ID, a.ITEM_GROUP_ID,
		sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)) as RCV,
		sum((case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as ISSUE, 
		sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as BALANCE 
		from product_details_master a, order_wise_pro_details b, inv_transaction c
		where a.id=b.prod_id and b.trans_id=c.id and c.item_category=4 and c.store_id =$store_id and b.po_breakdown_id in($po_id) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(24,25,49,73,78,112)  
		group by b.PO_BREAKDOWN_ID, b.PROD_ID, a.ITEM_GROUP_ID";
		$stock_sql_result=sql_select($stock_sql);
		$item_stock_arr=array();
		foreach($stock_sql_result as $val)
		{
			$item_stock_arr[$val["PROD_ID"]][$val["PO_BREAKDOWN_ID"]]=$val["BALANCE"];
		}
		
		$po_sql="SELECT a.ID, a.JOB_NO, a.PO_BREAK_DOWN_ID, p.PO_NUMBER, a.PRE_COST_FABRIC_COST_DTLS_ID, a.TRIM_GROUP, a.UOM, b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.DESCRIPTION, c.PROD_ID, sum(b.CONS) as WO_QNTY, sum(c.QUANTITY) as RCV_QNTY
		from WO_PO_BREAK_DOWN p, WO_BOOKING_DTLS a, WO_TRIM_BOOK_CON_DTLS b, ORDER_WISE_PRO_DETAILS c, PRODUCT_DETAILS_MASTER d
		where p.id=a.PO_BREAK_DOWN_ID and a.id=b.WO_TRIM_BOOKING_DTLS_ID and b.PO_BREAK_DOWN_ID=c.PO_BREAKDOWN_ID and c.PROD_ID=d.id and a.TRIM_GROUP=d.item_group_id and b.COLOR_NUMBER_ID=d.COLOR and b.GMTS_SIZES=d.GMTS_SIZE and b.ITEM_COLOR=d.ITEM_COLOR and b.ITEM_SIZE=d.ITEM_SIZE and b.DESCRIPTION=d.ITEM_DESCRIPTION and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.ENTRY_FORM in(24,78,112) and a.PO_BREAK_DOWN_ID in($po_id) and b.COLOR_NUMBER_ID=$gmst_color_id
		group by a.ID, a.JOB_NO, a.PO_BREAK_DOWN_ID, p.PO_NUMBER, a.PRE_COST_FABRIC_COST_DTLS_ID, a.TRIM_GROUP, a.UOM, b.COLOR_NUMBER_ID, b.GMTS_SIZES, b.ITEM_COLOR, b.ITEM_SIZE, b.DESCRIPTION, c.PROD_ID";
	}
	else
	{
		$po_sql="SELECT a.ID, a.JOB_NO, a.PO_BREAK_DOWN_ID, p.PO_NUMBER, a.PRE_COST_FABRIC_COST_DTLS_ID, a.TRIM_GROUP, a.UOM, b.COLOR_NUMBER_ID, b.DESCRIPTION, 0 as PROD_ID, sum(b.CONS) as WO_QNTY, 0 as RCV_QNTY
		from WO_PO_BREAK_DOWN p, WO_BOOKING_DTLS a, WO_TRIM_BOOK_CON_DTLS b
		where p.id=a.PO_BREAK_DOWN_ID and a.id=b.WO_TRIM_BOOKING_DTLS_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.PO_BREAK_DOWN_ID in($po_id) and b.COLOR_NUMBER_ID=$gmst_color_id
		group by a.ID, a.JOB_NO, a.PO_BREAK_DOWN_ID, p.PO_NUMBER, a.PRE_COST_FABRIC_COST_DTLS_ID, a.TRIM_GROUP, a.UOM, b.COLOR_NUMBER_ID, b.DESCRIPTION";
		//echo $po_sql;
	}
	
	//echo $po_sql; //die;
	$result = sql_select($po_sql); $po_data_arr=array();
	/*foreach($result as $row)
	{
		if($row['WO_QNTY']>0)
		{
			$key=$row['PO_BREAK_DOWN_ID'].'_'.$row['TRIM_GROUP'].'_'.$row['DESCRIPTION'].'_'.$row['COLOR_NUMBER_ID'];
			$po_data_arr[$key]['book_dtls_id']=$row['ID'];		
			$po_data_arr[$key]['po_id']=$row['PO_BREAK_DOWN_ID'];
			$po_data_arr[$key]['po_id']=$row['PO_BREAK_DOWN_ID'];
			$po_data_arr[$key]['trim_cost_dtls_id']=$row['PRE_COST_FABRIC_COST_DTLS_ID'];
			$po_data_arr[$key]['item_group_id']=$row['TRIM_GROUP'];
			$po_data_arr[$key]['item_description']=$row['DESCRIPTION'];
			$po_data_arr[$key]['uom']=$row['UOM'];
			$po_data_arr[$key]['job_no']=$row['JOB_NO'];
			$po_data_arr[$key]['gmst_color_id']=$row['COLOR_NUMBER_ID'];
			$po_data_arr[$key]['book_qnty']=$row['WO_QNTY'];
		}
	}*/
	
	$i=0;
	foreach($result as $row)
	{
		$i++;
		$required_qnty=$poReqDataArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']][$row['PO_BREAK_DOWN_ID']][$row['COLOR_NUMBER_ID']]['REQUIRED_QNTY'];
		$cons_dzn_gmts=$poReqDataArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']][$row['PO_BREAK_DOWN_ID']][$row['COLOR_NUMBER_ID']]['CONS_DZN_GMTS'];
		$prev_req_qnty=$prv_req_data[$row['PO_BREAK_DOWN_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['DESCRIPTION']))][$row['COLOR_NUMBER_ID']];
		$stock_qnty=$item_stock_arr[$row['PROD_ID']][$row['PO_BREAK_DOWN_ID']];
		//echo $prev_req_qnty."=".$row['PO_BREAK_DOWN_ID']."=".$row['TRIM_GROUP']."=".strtoupper(trim($row['DESCRIPTION']))."=".$row['COLOR_NUMBER_ID'];die;
		if($variable_data_source==2) $balance_qnty=$row['RCV_QNTY']-$prev_req_qnty;
		else $balance_qnty=$row['WO_QNTY']-$prev_req_qnty;
		
		if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"> 
			<td align="center" id="slTd_<?= $i;?>"><? echo $i; ?></td>
            <td align="center" id="tdPoNo_<?= $i;?>" title="<?= $row['PO_BREAK_DOWN_ID'];?>"><? echo $row['PO_NUMBER']; ?></td>
            <td align="center" id="tdItemGroup_<?= $i;?>" title="<?= $row['TRIM_GROUP'];?>"><? echo $item_group_arr[$row['TRIM_GROUP']]; ?></td>
            <td id="tdItemDescrip_<?= $i;?>" title="<?= $row['DESCRIPTION'];?>"><? echo $row['DESCRIPTION']; ?></td>
			<td align="right" id="tdBudget_<?= $i;?>"><? echo $cons_dzn_gmts; ?></td>
            <td align="center" id="tdUom_<?= $i;?>" title="<?= $row['UOM'];?>"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
            <td align="right" id="tdRrequiredQnty_<?= $i;?>"><? echo number_format($required_qnty,4,'.',''); ?></td>
            <td align="right" id="tdBookQnty_<?= $i;?>"><? echo number_format($row['WO_QNTY'],4,'.',''); ?></td>
            <td align="right" id="tdStockQnty_<?= $i;?>"><? echo number_format($stock_qnty,4,'.',''); ?></td>
            <td align="right" id="tdStoreRcvQnty_<?= $i;?>"><? echo number_format($row['RCV_QNTY'],4,'.',''); ?></td>
            <td align="right" id="tdPrevReqQnty_<?= $i;?>"><? echo number_format($prev_req_qnty,4,'.',''); ?></td>
            <td align="center" id="tdReqQnty_<?= $i;?>">
			<input type="text" name="txtReqQnty[]" id="txtReqQnty_<?= $i;?>" class="text_boxes_numeric" placeholder="" value="" style="width:80px" />
			</td>
            <td align="right" id="tdTotReqQnty_<?= $i;?>"><? echo number_format($prev_req_qnty,4,'.',''); ?></td>
            <td align="right" id="tdBalanceQnty_<?= $i;?>"><? echo number_format($balance_qnty,4,'.',''); ?></td>
            <td align="center" id="tdReqQnty_<?= $i;?>">
			<input type="text" name="txtRemarks[]" id="txtRemarks_<?= $i;?>" class="text_boxes" value="" style="width:80px" />
			<input type="hidden" id="hdnUpdateDtlsId_<?= $i;?>" name="hdnUpdateDtlsId[]" />
            <input type="hidden" id="hdnPoId_<?= $i;?>" name="hdnPoId[]" value="<? echo $row['PO_BREAK_DOWN_ID']; ?>" />            
            <input type="hidden" id="hdnBookDtlsId_<?= $i;?>" name="hdnBookDtlsId[]" value="<? echo $row['ID']; ?>" />
            <input type="hidden" id="hdnPrecosDtlsId_<?= $i;?>" name="hdnPrecosDtlsId[]" value="<? echo $row['PRE_COST_FABRIC_COST_DTLS_ID']; ?>" />
            <input type="hidden" id="hdnJobNo_<?= $i;?>" name="hdnJobNo[]" value="<? echo $row['JOB_NO']; ?>" />
            <input type="hidden" id="hdnColorId_<?= $i;?>" name="hdnColorId[]" value="<? echo $row['COLOR_NUMBER_ID']; ?>" />
            <input type="hidden" id="hdnProdId_<?= $i;?>" name="hdnProdId[]" value="<? echo $row['PROD_ID']; ?>" />
			</td>
		</tr>
		<?
	}
	die;
}

if($action=="product_details_update_input")
{
	//echo $sqlpo; die;
	$data_ref=explode("__",$data);
	$po_id=$data_ref[0];
	$gmst_color_id=$data_ref[1];
	$mst_id=$data_ref[2];
	$variable_data_source=$data_ref[3];
	$store_id=$data_ref[4];
	
	if($variable_data_source==2)
	{
		$stock_sql="select b.PO_BREAKDOWN_ID, b.PROD_ID, a.ITEM_GROUP_ID,
		sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)) as RCV,
		sum((case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as ISSUE, 
		sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as BALANCE 
		from product_details_master a, order_wise_pro_details b, inv_transaction c
		where a.id=b.prod_id and b.trans_id=c.id and c.item_category=4 and c.store_id =$store_id and b.po_breakdown_id in($po_id) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(24,25,49,73,78,112)  
		group by b.PO_BREAKDOWN_ID, b.PROD_ID, a.ITEM_GROUP_ID";
		$stock_sql_result=sql_select($stock_sql);
		$item_stock_arr=array();
		foreach($stock_sql_result as $val)
		{
			$item_stock_arr[$val["PROD_ID"]][$val["PO_BREAKDOWN_ID"]]=$val["BALANCE"];
		}
	}
	
	$req_sql="SELECT c.ID, c.MST_ID, c.JOB_NO, c.PO_ID, d.PO_NUMBER, c.COLOR_SIZE_TABLE_ID, c.PRECOST_TRIM_DTLS_ID, c.TRIM_GROUP, c.ITEM_DESCRIPTION, c.GMTS_COLOR_ID, c.ENTRY_FORM, c.CONS_UOM, c.CONS, c.REQSN_QTY, c.REMARKS, c.PRODUCT_ID, c.SIZE_INPUT_QTY as RCV_QNTY
	from ready_to_sewing_reqsn c, wo_po_break_down d
	where c.PO_ID=d.id and c.entry_form in(357) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id and c.PO_ID=$po_id and c.GMTS_COLOR_ID=$gmst_color_id";
	// echo $po_sql; die;
	$result = sql_select($req_sql);
	
	$po_data_arr=array();$all_po_id=$all_gmts_color_id=array();
	foreach($result as $row)
	{
		$all_po_id[$row['PO_ID']]=$row['PO_ID'];
		$all_gmts_color_id[$row['GMTS_COLOR_ID']]=$row['GMTS_COLOR_ID'];
			
		$key=$row['PO_ID'].'_'.$row['TRIM_GROUP'].'_'.$row['ITEM_DESCRIPTION'].'_'.$row['GMTS_COLOR_ID'].'_'.$row['ID'];
		$po_data_arr[$key]['book_dtls_id']=$row['COLOR_SIZE_TABLE_ID'];		
		$po_data_arr[$key]['po_id']=$row['PO_ID'];
		$po_data_arr[$key]['trim_cost_dtls_id']=$row['PRECOST_TRIM_DTLS_ID'];
		$po_data_arr[$key]['item_group_id']=$row['TRIM_GROUP'];
		$po_data_arr[$key]['item_description']=$row['ITEM_DESCRIPTION'];
		$po_data_arr[$key]['uom']=$row['CONS_UOM'];
		$po_data_arr[$key]['job_no']=$row['JOB_NO'];
		$po_data_arr[$key]['po_number']=$row['PO_NUMBER'];
		$po_data_arr[$key]['gmst_color_id']=$row['GMTS_COLOR_ID'];
		$po_data_arr[$key]['book_qnty']=$row['CONS'];
		$po_data_arr[$key]['reqsn_qty']=$row['REQSN_QTY'];
		$po_data_arr[$key]['dtls_id']=$row['ID'];
		$po_data_arr[$key]['remarks']=$row['REMARKS'];
		$po_data_arr[$key]['product_id']=$row['PRODUCT_ID'];
		$po_data_arr[$key]['rcv_qnty']=$row['RCV_QNTY'];
	}
	
	$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, d.costing_per_id AS COSTING_PER 
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d 
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id in($po_id)";
	//echo $sqlpo; die; //and a.job_no='$job_no'
	$sqlpoRes = sql_select($sqlpo);
	//print_r($sqlpoRes); die;
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
	foreach($sqlpoRes as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$job_id=$row['JOB_ID'];
	}
	unset($sqlpoRes);
	
	$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO 
	from wo_po_details_mas_set_details a
	where a.job_id=$job_id";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
	}
	unset($gmtsitemRatioSqlRes);
	
	$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts as CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
	where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=$po_id";
	//echo $sqlTrim; die;
	$sqlTrimRes = sql_select($sqlTrim);
	
	foreach($sqlTrimRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
		//print_r($poCountryId);
		
		if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
		{
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			
			$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
			$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
			
			$consAmt=$consQnty*$row['RATE'];
			$consTotAmt=$consTotQnty*$row['RATE'];
		}
		else
		{
			
			$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
			$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			foreach($poCountryId as $countryId)
			{
				if(in_array($countryId, $countryIdArr))
				{
					$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					$consQty=$consTotQty=0;
					
					$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
					//echo "2=".$poQty."=".$itemRatio."=".$row['CONS']."=".$costingPer;die;
					$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
					
					$consQnty+=$consQty;
					$consTotQnty+=$consTotQty;
					//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
					$consAmt+=$consQty*$row['RATE'];
					//$consTotAmt+=$consTotQty*$row['RATE'];
					$consTotAmt+=$consTotQnty*$row['RATE'];
				}
			}
		}
		//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
		$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['REQUIRED_QNTY']+=$consTotQnty;
		$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['CONS_DZN_GMTS']=$row['CONS_DZN_GMTS'];
		
	}
	//echo "<pre>";print_r($poReqDataArr);die;
	unset($sqlTrimRes);
	
	
	$prv_req_sql="SELECT a.ID as REQ_ID, b.PO_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.GMTS_COLOR_ID, b.REQSN_QTY 
	from ready_to_sewing_reqsn_mst a, ready_to_sewing_reqsn b 
	where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 and b.PO_ID=$po_id and b.GMTS_COLOR_ID=$gmst_color_id and a.id<>$mst_id";
	// echo $prv_req_sql;die;
	$prv_req_result=sql_select($prv_req_sql);
	$pre_issue_data=array();
	if(count($prv_req_result)>0)
	{
		foreach($prv_req_result as $row)
		{
			$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']]+=$row['REQSN_QTY'];
		}

	}
	
	$i=$dtls_tbl_length;
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where item_category=4",'id','item_name');
	foreach($po_data_arr as $key=> $row)
	{
		$i++;
		$required_qnty=$poReqDataArr[$row['trim_cost_dtls_id']][$row['po_id']][$row['gmst_color_id']]['REQUIRED_QNTY'];
		$cons_dzn_gmts=$poReqDataArr[$row['trim_cost_dtls_id']][$row['po_id']][$row['gmst_color_id']]['CONS_DZN_GMTS'];
		$prev_req_qnty=$prv_req_data[$row['po_id']][$row['item_group_id']][strtoupper(trim($row['item_description']))][$row['gmst_color_id']];
		//$balance_qnty=$required_qnty-($prev_req_qnty+$row['reqsn_qty']);
		if($variable_data_source==2) 
		{
			$stock_qnty=$item_stock_arr[$row['product_id']][$row['po_id']];
			$balance_qnty=$row['rcv_qnty']-$prev_req_qnty;
		}
		else 
		{
			$balance_qnty=$row['book_qnty']-$prev_req_qnty;
		}
		
		if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"> 
			<td align="center" id="slTd_<?= $i;?>" title="<?= $key; ?>"><? echo $i; ?></td>
            <td align="center" id="tdPoNo_<?= $i;?>" title="<?= $row['po_id'];?>"><? echo $row['po_number']; ?></td>
            <td align="center" id="tdItemGroup_<?= $i;?>" title="<?= $row['item_group_id'];?>"><? echo $item_group_arr[$row['item_group_id']]; ?></td>
            <td id="tdItemDescrip_<?= $i;?>" title="<?= $row['item_description'];?>"><? echo $row['item_description']; ?></td>
			<td align="right" id="tdBudget_<?= $i;?>"><? echo $cons_dzn_gmts; ?></td>
            <td align="center" id="tdUom_<?= $i;?>" title="<?= $row['uom'];?>"><? echo $unit_of_measurement[$row['uom']]; ?></td>
            <td align="right" id="tdRrequiredQnty_<?= $i;?>"><? echo number_format($required_qnty,4,'.',''); ?></td>
            <td align="right" id="tdBookQnty_<?= $i;?>"><? echo number_format($row['book_qnty'],4,'.',''); ?></td>
            <td align="right" id="tdStockQnty_<?= $i;?>"><? echo number_format($stock_qnty,4,'.',''); ?></td>
            <td align="right" id="tdStoreRcvQnty_<?= $i;?>"><? echo number_format($row['rcv_qnty'],4,'.',''); ?></td>
            <td align="right" id="tdPrevReqQnty_<?= $i;?>"><? echo number_format($prev_req_qnty,4,'.',''); ?></td>
            <td align="center" id="tdReqQnty_<?= $i;?>">
			<input type="text" name="txtReqQnty[]" id="txtReqQnty_<?= $i;?>" class="text_boxes_numeric" placeholder="" value="<? echo number_format($row['reqsn_qty'],4,'.',''); ?>" style="width:80px" />
			</td>
            <td align="right" id="tdTotReqQnty_<?= $i;?>"><? echo number_format($prev_req_qnty+$row['reqsn_qty'],4,'.',''); ?></td>
            <td align="right" id="tdBalanceQnty_<?= $i;?>"><? echo number_format($balance_qnty,4,'.',''); ?></td>
            <td align="center">
			<input type="text" name="txtRemarks[]" id="txtRemarks_<?= $i;?>" class="text_boxes" value="<? echo $row['remarks']; ?>" style="width:80px" />
			<input type="hidden" id="hdnUpdateDtlsId_<?= $i;?>" name="hdnUpdateDtlsId[]" value="<? echo $row['dtls_id']; ?>" />
            <input type="hidden" id="hdnPoId_<?= $i;?>" name="hdnPoId[]" value="<? echo $row['po_id']; ?>" />            
            <input type="hidden" id="hdnBookDtlsId_<?= $i;?>" name="hdnBookDtlsId[]" value="<? echo $row['book_dtls_id']; ?>" />
            <input type="hidden" id="hdnPrecosDtlsId_<?= $i;?>" name="hdnPrecosDtlsId[]" value="<? echo $row['trim_cost_dtls_id']; ?>" />
            <input type="hidden" id="hdnJobNo_<?= $i;?>" name="hdnJobNo[]" value="<? echo $row['job_no']; ?>" />
            <input type="hidden" id="hdnColorId_<?= $i;?>" name="hdnColorId[]" value="<? echo $row['gmst_color_id']; ?>" />
            <input type="hidden" id="hdnProdId_<?= $i;?>" name="hdnProdId[]" value="<? echo $row['product_id']; ?>" />
			</td>
		</tr>
		<?
	}
	die;
}


if($action=="product_details_update")
{
	$req_sql="SELECT c.ID, c.MST_ID, c.JOB_NO, c.PO_ID, d.PO_NUMBER, c.COLOR_SIZE_TABLE_ID, c.PRECOST_TRIM_DTLS_ID, c.TRIM_GROUP, c.ITEM_DESCRIPTION, c.GMTS_COLOR_ID, c.ENTRY_FORM, c.CONS_UOM, c.CONS, c.REQSN_QTY, c.REMARKS
	from ready_to_sewing_reqsn c, wo_po_break_down d
	where c.PO_ID=d.id and c.entry_form in(357) and c.status_active=1 and c.is_deleted=0 and mst_id=$data";
	//echo $po_sql;//  die;
	$result = sql_select($req_sql);
	
	$po_data_arr=array();$all_po_id=$all_gmts_color_id=array();
	foreach($result as $row)
	{
		$all_po_id[$row['PO_ID']]=$row['PO_ID'];
		$all_gmts_color_id[$row['GMTS_COLOR_ID']]=$row['GMTS_COLOR_ID'];
			
		$key=$row['PO_ID'].'_'.$row['TRIM_GROUP'].'_'.$row['ITEM_DESCRIPTION'].'_'.$row['GMTS_COLOR_ID'].'_'.$row['ID'];
		$po_data_arr[$key]['book_dtls_id']=$row['COLOR_SIZE_TABLE_ID'];		
		$po_data_arr[$key]['po_id']=$row['PO_ID'];
		$po_data_arr[$key]['trim_cost_dtls_id']=$row['PRECOST_TRIM_DTLS_ID'];
		$po_data_arr[$key]['item_group_id']=$row['TRIM_GROUP'];
		$po_data_arr[$key]['item_description']=$row['ITEM_DESCRIPTION'];
		$po_data_arr[$key]['uom']=$row['CONS_UOM'];
		$po_data_arr[$key]['job_no']=$row['JOB_NO'];
		$po_data_arr[$key]['po_number']=$row['PO_NUMBER'];
		$po_data_arr[$key]['gmst_color_id']=$row['GMTS_COLOR_ID'];
		$po_data_arr[$key]['book_qnty']=$row['CONS'];
		$po_data_arr[$key]['reqsn_qty']=$row['REQSN_QTY'];
		$po_data_arr[$key]['dtls_id']=$row['ID'];
		$po_data_arr[$key]['remarks']=$row['REMARKS'];
	}
	
	$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, d.costing_per_id AS COSTING_PER 
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d 
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id in(".implode(",",$all_po_id).")";
	//echo $sqlpo; die; //and a.job_no='$job_no'
	$sqlpoRes = sql_select($sqlpo);
	//print_r($sqlpoRes); die;
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
	foreach($sqlpoRes as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$job_id_arr[$row['JOB_ID']]=$row['JOB_ID'];
	}
	unset($sqlpoRes);
	
	$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO 
	from wo_po_details_mas_set_details a
	where a.job_id in(".implode(",",$job_id_arr).")";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
	}
	unset($gmtsitemRatioSqlRes);
	
	$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts as CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
	where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in(".implode(",",$all_po_id).")";
	//echo $sqlTrim; die;
	$sqlTrimRes = sql_select($sqlTrim);
	
	foreach($sqlTrimRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
		//print_r($poCountryId);
		
		if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
		{
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			
			$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
			$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
			
			$consAmt=$consQnty*$row['RATE'];
			$consTotAmt=$consTotQnty*$row['RATE'];
		}
		else
		{
			
			$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
			$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			foreach($poCountryId as $countryId)
			{
				if(in_array($countryId, $countryIdArr))
				{
					$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					$consQty=$consTotQty=0;
					
					$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
					//echo "2=".$poQty."=".$itemRatio."=".$row['CONS']."=".$costingPer;die;
					$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
					
					$consQnty+=$consQty;
					$consTotQnty+=$consTotQty;
					//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
					$consAmt+=$consQty*$row['RATE'];
					//$consTotAmt+=$consTotQty*$row['RATE'];
					$consTotAmt+=$consTotQnty*$row['RATE'];
				}
			}
		}
		//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
		$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['REQUIRED_QNTY']+=$consTotQnty;
		$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['CONS_DZN_GMTS']=$row['CONS_DZN_GMTS'];
		
	}
	//echo "<pre>";print_r($poReqDataArr);die;
	unset($sqlTrimRes);
	
	
	$prv_req_sql="SELECT a.ID as REQ_ID, b.PO_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.GMTS_COLOR_ID, b.REQSN_QTY 
	from ready_to_sewing_reqsn_mst a, ready_to_sewing_reqsn b 
	where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 and b.PO_ID in(".implode(",",$all_po_id).") and b.GMTS_COLOR_ID in(".implode(",",$all_gmts_color_id).") and a.id<>$data";
	// echo $prv_req_sql;die;
	$prv_req_result=sql_select($prv_req_sql);
	$pre_issue_data=array();
	if(count($prv_req_result)>0)
	{
		foreach($prv_req_result as $row)
		{
			$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']]+=$row['REQSN_QTY'];
		}

	}
	
	$i=$dtls_tbl_length;
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where item_category=4",'id','item_name');
	?>
    <table cellpadding="0" cellspacing="1" width="1350" class="rpt_table" border="1" rules="all" id="tbl_list_view" align="left">
    <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Job No</th>
                <th width="100">PO No</th>
                <th width="120">Item Name</th>
                <th width="120">Item Description</th>
                <th width="80">Consumption / DZN</th>
                <th width="60">UOM</th>
                <th width="100">Required Qty</th>
                <th width="100">Booking Qty</th>
                <th width="100">Cumu. Requisition Qty</th>
                <th width="100">Current Req. Qty</th>
                <th width="100">Total Req. Qty</th>
                <th width="100">Req. Balance</th>
                <th>Remarks</th>
                
            </tr>
        </thead>
        <tbody>
        <?
		$i=0;
		foreach($po_data_arr as $key=> $row)
		{
			$i++;
			$required_qnty=$poReqDataArr[$row['trim_cost_dtls_id']][$row['po_id']][$row['gmst_color_id']]['REQUIRED_QNTY'];
			$cons_dzn_gmts=$poReqDataArr[$row['trim_cost_dtls_id']][$row['po_id']][$row['gmst_color_id']]['CONS_DZN_GMTS'];
			$prev_req_qnty=$prv_req_data[$row['po_id']][$row['item_group_id']][strtoupper(trim($row['item_description']))][$row['gmst_color_id']];
			$balance_qnty=$required_qnty-($prev_req_qnty+$row['reqsn_qty']);
			if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="fn_item_details('<? echo $row['po_id']; ?>','<? echo $row['gmst_color_id']; ?>','<? echo $data; ?>');"> 
				<td align="center" title="<?= $key; ?>"><? echo $i; ?></td>
				<td><? echo $row['job_no']; ?></td>
				<td><? echo $row['po_number']; ?></td>
				<td><? echo $item_group_arr[$row['item_group_id']]; ?></td>
				<td><? echo $row['item_description']; ?></td>
				<td align="right"><? echo $cons_dzn_gmts; ?></td>
				<td align="center"><? echo $unit_of_measurement[$row['uom']]; ?></td>
				<td align="right"><? echo number_format($required_qnty,4,'.',''); ?></td>
				<td align="right"><? echo number_format($row['book_qnty'],4,'.',''); ?></td>
				<td align="right"><? echo number_format($prev_req_qnty,4,'.',''); ?></td>
				<td align="right"><? echo number_format($row['reqsn_qty'],4,'.',''); ?></td>
				<td align="right"><? echo number_format($prev_req_qnty+$row['reqsn_qty'],4,'.',''); ?></td>
				<td align="right"><? echo number_format($balance_qnty,4,'.',''); ?></td>
				<td align="center"><? echo $row['remarks']; ?></td>
			</tr>
			<?
		}
		
		?>
        </tbody>    
    </table>
    <?
	die;
}




if($action=="com_wise_variable_data")
{
	$nameArray= sql_select("select id, production_entry, process_loss_editable, allocation_control from variable_settings_production where company_name=$data and variable_list=84 and status_active=1 and is_deleted=0");
	if(count($nameArray)>0)
	{
		echo $nameArray[0][csf('production_entry')]."**".$nameArray[0][csf('process_loss_editable')]."**".$nameArray[0][csf('allocation_control')];
	}
	else
	{
		echo "0**0**0";
	}
	die;
}


if($action=="delivery_system_popup") //System PopUp
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
		<script>
		function js_set_value(str)
		{
	 		$("#hidden_return_id").val(str);
	    	parent.emailwindow.hide();
	 	}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="1030" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	             <thead>
	                <th width="160">Working Company</th>
	                <th width="160">Buyer Name</th>
                    <th width="100">Job Year</th>
	                <th width="120">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="120">Req No</th>
	                <th width="200">Req Date</th>
	                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	            </thead>
	            <tr class="general">
	                <td>
	                <?
	                echo create_drop_down( "cbo_trans_com", 150, "SELECT id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", str_replace("'","",$company), "",0 );
	                ?>
	                </td>
	                <td>
	                <?
						echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (SELECT  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
					?>
	                </td>
                    <td>
	                <?
	                echo create_drop_down( "cbo_job_year", 80, $year ,"", 1, "-- Select --", date("Y"), "",0 );
	                ?>
	                </td>
					 <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
	                </td>
	                <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
	                </td>
                    <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_req_no" id="txt_req_no" />
	                </td>
	                <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" readonly> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" readonly>
	                </td>
	                <td align="center">
	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_com').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value,'create_return_search_list', 'search_div_delivery', 'trims_issue_requisition_v2_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:80px;" />
	                </td>
	            </tr>
	            <tr>
	                <td align="center" height="40" colspan="8" valign="middle">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_return_id" >
	                </td>
	            </tr>
	        </table>
	        <div id="search_div_delivery" style="margin-top:20px;"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
//$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

if($action=="create_return_search_list")
{
	//echo $data;die;
 	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$buyer_id = str_replace("'","",$ex_data[1]);
	$req_no = $ex_data[2];
	$job_year = str_replace("'","",$ex_data[3]);	
	$job_no = str_replace("'","",$ex_data[4]);
	$style_no = str_replace("'","",$ex_data[5]);
	$txt_date_from = $ex_data[6];
	$txt_date_to = $ex_data[7];
	//$company = $ex_data[4];
	
	//echo $trans_com;die;
	$sql_cond="";
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and a.REQUISITION_DATE between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.REQUISITION_DATE between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	$sql_cond .= " and a.WORKING_COMPANY_ID  ='$company'";
	if(trim($buyer_id)!=0) $sql_cond .= " and d.BUYER_NAME='$buyer_id'";
	if($job_year>0) $sql_cond .= " and to_char(d.insert_date,'YYYY')='$job_year'";
	if(trim($job_no)!="") $sql_cond .= " and d.JOB_NO_PREFIX_NUM='$job_no'";
	if(trim($style_no)!="") $sql_cond .= " and d.STYLE_REF_NO='$style_no'";
	if(trim($req_no)!="") $sql_cond .= " and a.REQ_NO_PREFIX_NUM='$req_no'";
	
	if($db_type==0) $select_year="year(a.INSERT_DATE)"; else $select_year="to_char(a.insert_date,'YYYY')";
	
	$sql = "SELECT a.ID, a.REQ_NO_PREFIX_NUM, $select_year as REQ_YEAR, a.REQ_NO, a.WORKING_COMPANY_ID, a.REQUISITION_DATE, a.DELIVERY_DATE, d.BUYER_NAME, d.JOB_NO, d.STYLE_REF_NO, b.GMTS_COLOR_ID
	from  READY_TO_SEWING_REQSN b, WO_PO_BREAK_DOWN c, WO_PO_DETAILS_MASTER d, READY_TO_SEWING_REQSN_MST a
	where a.ID=b.MST_ID and b.PO_ID=c.ID and c.JOB_NO_MST=d.JOB_NO and a.ENTRY_FORM=357 and a.REQUISITION_VERSION=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 $sql_cond
	group by  a.ID, a.REQ_NO_PREFIX_NUM, a.insert_date, a.REQ_NO, a.WORKING_COMPANY_ID, a.REQUISITION_DATE, a.DELIVERY_DATE, d.BUYER_NAME, d.JOB_NO, d.STYLE_REF_NO, b.GMTS_COLOR_ID
	order by a.ID desc";
	//echo $sql;die;
	$result = sql_select($sql);
	// echo "<pre>";print_r($result);die;
	$company_arr=return_library_array( "SELECT ID, COMPANY_NAME from LIB_COMPANY",'ID','COMPANY_NAME');
	$buyer_arr=return_library_array( "SELECT ID, BUYER_NAME from LIB_BUYER",'ID','BUYER_NAME');
	$color_arr=return_library_array( "SELECT ID, COLOR_NAME from LIB_COLOR where STATUS_ACTIVE=1 and IS_DELETED=0",'ID','COLOR_NAME');
	
   ?>
    <table cellspacing="0" width="1020" style="margin-right:18px;" class="rpt_table" cellpadding="0" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="170">Working Company</th>
            <th width="70">Requisition Date</th>
            <th width="70">Delivery Date</th>
            <th width="50">Year</th>
            <th width="120">Sys Num</th>
            <th width="160">Buyer Name</th>
            <th width="100">Job No</th>
            <th width="120">Style Ref No</th>
            <th>Color</th>
        </thead>
    </table>
	<div style="width:1040px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="1020" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list" align="left">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				//$buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number='".$row[csf('challan_no')]."' and entry_form!=85 ","buyer_id");
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row['ID'];?>);" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="170"><p><? echo $company_arr[$row["WORKING_COMPANY_ID"]];?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row["REQUISITION_DATE"]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row["DELIVERY_DATE"]); ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row["REQ_YEAR"]; ?></p></td>
                    <td width="120" align="center"><p><? echo $row["REQ_NO"]; ?></p></td>
                    <td width="160"><p><? echo $buyer_arr[$row["BUYER_NAME"]]; ?>&nbsp;</p></td>
                    <td width="100" align="center"><p><? echo $row["JOB_NO"]; ?></p></td>
                    <td width="120" align="center"><p><? echo $row["STYLE_REF_NO"]; ?></p></td>
                    <td><p><? echo $color_arr[$row["GMTS_COLOR_ID"]]; ?>&nbsp;</p></td>
                </tr>
				<?
				$i++;
             }
   			?>
		</table>
	</div>
	<?
	exit();

}

if($action=="populate_master_from_data") //Master Part
{
	$sql_mst=sql_select("select ID, REQ_NO, COMPANY_ID, LOCATION_ID, REQUISITION_DATE, DELIVERY_DATE, STORE_ID, WORKING_COMPANY_ID, WORKING_LOCATION_ID, FLOOR_ID, SEWING_LINE, TRIM_TYPE, REMARKS, STORE_ID
	from READY_TO_SEWING_REQSN_MST where ID=$data");
	foreach($sql_mst as $row)
	{
		echo "$('#txt_req_no').val('".$row['REQ_NO']."');\n";
		echo "$('#update_id').val('".$row['ID']."');\n";
		//echo "$('#cbo_company_name').val(".$row['COMPANY_ID'].");\n";
		//echo "$('#cbo_company_name').attr('disabled',true);\n";
        
		//echo  "load_drop_down( 'requires/trims_issue_requisition_v2_controller', ".$row['COMPANY_ID'].", 'load_drop_down_location', 'location_td' );\n";

		//echo "$('#cbo_location_name').val(".$row['LOCATION_ID'].");\n";
		
		//echo "$('#cbo_store_name').val('".$row['STORE_ID']."');\n";
		echo "$('#txt_req_date').val('".change_date_format($row['REQUISITION_DATE'])."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row['DELIVERY_DATE'])."');\n";
		echo "$('#cbo_working_company').val(".$row['WORKING_COMPANY_ID'].");\n";
		echo  "load_drop_down( 'requires/trims_issue_requisition_v2_controller', ".$row['WORKING_COMPANY_ID'].", 'load_drop_down_working_location', 'working_location_td' );\n";
        echo "$('#cbo_working_location').val(".$row['WORKING_LOCATION_ID'].");\n";
		if($row['WORKING_LOCATION_ID']>0)
		{
			echo "load_drop_down('requires/trims_issue_requisition_v2_controller', ".$row['WORKING_LOCATION_ID']."+'__'+".$row['WORKING_COMPANY_ID'].",'load_drop_down_floor','floor_td' );\n";
			echo "load_drop_down( 'requires/trims_issue_requisition_v2_controller', ".$row['WORKING_LOCATION_ID']."+'__'+".$row['WORKING_COMPANY_ID'].", 'load_drop_down_store', 'store_id' );\n";
		}
		echo "$('#cbo_floor_name').val('".$row['FLOOR_ID']."');\n";
		echo "$('#cbo_store_name').val('".$row['STORE_ID']."');\n";
		if($row['FLOOR_ID']>0)
		{
			echo "load_drop_down( 'requires/trims_issue_requisition_v2_controller', ".$row['FLOOR_ID']."+'__'+".$row['WORKING_COMPANY_ID']."+'__'+".$row['WORKING_LOCATION_ID'].",'load_drop_down_sewing_line', 'sewing_td' );\n";
			//load_drop_down( 'requires/trims_issue_requisition_v2_controller', this.value+'__'+document.getElementById('cbo_working_company').value+'__'+document.getElementById('cbo_working_location').value, 'load_drop_down_sewing_line', 'sewing_td' );
		}
		echo "$('#cbo_sewing_line').val('".$row['SEWING_LINE']."');\n";
		echo "$('#cbo_trim_type').val('".$row['TRIM_TYPE']."');\n";
		echo "$('#txt_remarks').val('".$row['REMARKS']."');\n";
		echo "$('#cbo_trim_type').attr('disabled',true);\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN"); }
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		if(str_replace("'","",$update_id)=="")
		{
			$return_mst_id=return_next_id("id", "ready_to_sewing_reqsn_mst", 1);
			if($db_type==2) $mrr_cond=" and TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond=" and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_working_company), '', 'TIRE', date("Y",time()), 5, "select REQ_NO_PREFIX,REQ_NO_PREFIX_NUM from ready_to_sewing_reqsn_mst where WORKING_COMPANY_ID=$cbo_working_company and REQUISITION_VERSION=1 and entry_form=357 $mrr_cond order by id DESC ", "REQ_NO_PREFIX", "REQ_NO_PREFIX_NUM" ));
			
			$field_array_delivery="id, req_no_prefix, req_no_prefix_num, req_no, entry_form, working_company_id, working_location_id, floor_id, sewing_line, requisition_date, delivery_date, store_id, trim_type, remarks, requisition_version, inserted_by, insert_date";
			$data_array_delivery="(".$return_mst_id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',357,".$cbo_working_company.",".$cbo_working_location.",".$cbo_floor_name.",".$cbo_sewing_line.",".$txt_req_date.",".$txt_delivery_date.",".$cbo_store_name.",".$cbo_trim_type.",".$txt_remarks.",1,".$user_id.",'".$pc_date_time."')";
			//$mrr_no=$new_sys_number[0];
			$mrr_no=$new_sys_number[0];

		}
		else
		{
			$return_mst_id=str_replace("'","",$update_id);
			$mrr_no=str_replace("'","",$txt_req_no);
			$field_array_delivery="working_location_id*floor_id*sewing_line*requisition_date*delivery_date*trim_type*remarks*updated_by*update_date";
			$data_array_delivery="".$cbo_working_location."*".$cbo_floor_name."*".$cbo_sewing_line."*".$txt_req_date."*".$txt_delivery_date."*".$cbo_trim_type."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		}
		
		$field_array_dtls="id, mst_id, job_no, po_id, store_id, product_id, color_size_table_id, precost_trim_dtls_id, trim_group, item_description, gmts_color_id, entry_form, cons_uom, CONS, reqsn_qty, size_input_qty, stock_qnty, remarks, status_active, is_deleted, inserted_by, insert_date";
		
		$dtls_id=return_next_id("id", "ready_to_sewing_reqsn", 1);
		$data_array_dtls="";
		for($i=1;$i<=$row_num;$i++)
		{
			$job_no="job_no".$i;
			$gmts_color_id="gmts_color_id".$i;
			$order_id="order_id".$i;
			$book_dtls_id="book_dtls_id".$i;
			$pre_cost_dtls_id="pre_cost_dtls_id".$i;
			$item_group="item_group".$i;
			$itemdescription="itemdescription".$i;
			$cbouom="cbouom".$i;
			$bookQnty="bookQnty".$i;
			$reqQnty="reqQnty".$i;
			$reqQnty="reqQnty".$i;
			$txtRemarks="txtRemarks".$i;
			$updateId="updateId".$i;
			$prodId="prodId".$i;
			
			$store_rcv_qnty="store_rcv_qnty".$i;
			$stock_qnty="stock_qnty".$i;
			
			if($data_array_dtls !="") $data_array_dtls .=",";
			$data_array_dtls.="(".$dtls_id.",".$return_mst_id.",'".$$job_no."','".$$order_id."',".$cbo_store_name.",'".$$prodId."','".$$book_dtls_id."','".$$pre_cost_dtls_id."','".$$item_group."','".$$itemdescription."','".$$gmts_color_id."',357,'".$$cbouom."','".$$bookQnty."','".$$reqQnty."','".$$store_rcv_qnty."','".$$stock_qnty."','".$$txtRemarks."','1','0',".$user_id.",'".$pc_date_time."')";
			$dtls_id=$dtls_id+1;

		}
		//echo "10**".$data_array_dtls;die;
		$reqID=$reqDtlsID=true;
		if(str_replace("'","",$txt_req_no)=="")
		{
			$reqID=sql_insert("ready_to_sewing_reqsn_mst",$field_array_delivery,$data_array_delivery,1);
		}
		else
		{
			$reqID=sql_update("ready_to_sewing_reqsn_mst",$field_array_delivery,$data_array_delivery,"id",$return_mst_id,1);
		}
		$reqDtlsID=sql_insert("ready_to_sewing_reqsn",$field_array_dtls,$data_array_dtls,1);
		
		//echo "10**".$reqID."**".$reqDtlsID;oci_rollback($con);disconnect($con);die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($reqID && $reqDtlsID )
			{
				mysql_query("COMMIT");
				echo "0**".$return_mst_id."**".$mrr_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$return_mst_id."**".$mrr_no;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($reqID && $reqDtlsID )
			{
				oci_commit($con);
				echo "0**".$return_mst_id."**".$mrr_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$return_mst_id."**".$mrr_no;
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$return_mst_id=str_replace("'","",$update_id);
		$mrr_no=str_replace("'","",$txt_req_no);
		$field_array_delivery="working_location_id*floor_id*sewing_line*requisition_date*delivery_date*trim_type*remarks*updated_by*update_date";
		$data_array_delivery="".$cbo_working_location."*".$cbo_floor_name."*".$cbo_sewing_line."*".$txt_req_date."*".$txt_delivery_date."*".$cbo_trim_type."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		
		$field_array_dtls="id, mst_id, job_no, po_id, store_id, product_id, color_size_table_id, precost_trim_dtls_id, trim_group, item_description, gmts_color_id, entry_form, cons_uom, CONS, reqsn_qty, size_input_qty, stock_qnty, remarks, status_active, is_deleted, inserted_by, insert_date";
		$field_array_dtls_up="reqsn_qty*remarks*updated_by*update_date";
		$data_array_dtls="";
		$dtls_id=return_next_id("id", "ready_to_sewing_reqsn", 1);
		for($i=1;$i<=$row_num;$i++)
		{
			$job_no="job_no".$i;
			$gmts_color_id="gmts_color_id".$i;
			$order_id="order_id".$i;
			$book_dtls_id="book_dtls_id".$i;
			$pre_cost_dtls_id="pre_cost_dtls_id".$i;
			$item_group="item_group".$i;
			$itemdescription="itemdescription".$i;
			$cbouom="cbouom".$i;
			$bookQnty="bookQnty".$i;
			$reqQnty="reqQnty".$i;
			$txtRemarks="txtRemarks".$i;
			$updateId="updateId".$i;
			$prodId="prodId".$i;
			
			$store_rcv_qnty="store_rcv_qnty".$i;
			$stock_qnty="stock_qnty".$i;
			
			if(str_replace("'","",$$updateId) !="")
			{
				$updateID_array[]=str_replace("'","",$$updateId);
				$data_array_dtls_up[str_replace("'","",$$updateId)]=explode("*",("".$$reqQnty."*'".$$txtRemarks."'*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array_dtls !="") $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$return_mst_id.",'".$$job_no."','".$$order_id."',".$cbo_store_name.",'".$$prodId."','".$$book_dtls_id."','".$$pre_cost_dtls_id."','".$$item_group."','".$$itemdescription."','".$$gmts_color_id."',357,'".$$cbouom."','".$$bookQnty."','".$$reqQnty."','".$$store_rcv_qnty."','".$$stock_qnty."','".$$txtRemarks."','1','0',".$user_id.",'".$pc_date_time."')";
				$dtls_id=$dtls_id+1;
			}
		}
		$reqID=$dtlsrID=$reqDtlsID=true;
		$reqID=sql_update("ready_to_sewing_reqsn_mst",$field_array_delivery,$data_array_delivery,"id",$return_mst_id,1);
		$dtlsrID=execute_query(bulk_update_sql_statement("ready_to_sewing_reqsn","id",$field_array_dtls_up,$data_array_dtls_up,$updateID_array),1);
		if($data_array_dtls !="")
		{
			$reqDtlsID=sql_insert("ready_to_sewing_reqsn",$field_array_dtls,$data_array_dtls,1);
		}
		//echo "10** $reqID && $dtlsrID && $reqDtlsID";oci_rollback($con);die;	
		$return_mst_id=str_replace("'","",$update_id);
		$mrr_no=str_replace("'","",$txt_req_no);
		if($db_type==0)
		{
			if($reqID && $dtlsrID && $reqDtlsID)
			{
				mysql_query("COMMIT");
				echo "1**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($reqID && $dtlsrID && $reqDtlsID)
			{
				oci_commit($con);
				echo "1**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$return_mst_id=str_replace("'","",$update_id);
		$issue_sql=sql_select("select ID, ISSUE_NUMBER from INV_ISSUE_MASTER where status_active=1 and entry_form=25 and issue_basis=3 and booking_id=$return_mst_id");
		if(count($issue_sql)>0)
		{
			echo "20**Issue Found. Delete Not Allow";oci_rollback($con);disconnect($con);die;
		}
		$rID = execute_query("update ready_to_sewing_reqsn_mst set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where id=$return_mst_id");
		$dtlsrID = execute_query("update ready_to_sewing_reqsn set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id=$return_mst_id and status_active=1");

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_mst_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_mst_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_mst_id);
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="garments_exfactory_print")
{
	//$start = microtime(true);
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[1];
	$update_id=$data[2];
	echo load_html_head_contents("Trims Issue Requisition V2","../", 1, 1, $unicode,'','');
	//$com_dtls = fnc_company_location_address($company, $location, 2);
	$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where item_category=4",'id','item_name');
	$com_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where production_process=5 and status_active=1 and is_deleted=0", "id", "floor_name");
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$country_library=return_library_array( "select id, country_name from lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$company' and a.location_id='$location' and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number");
	//echo $line_data;die;

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
	
	$mst_sql=sql_select("SELECT a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, a.REMARKS
	from READY_TO_SEWING_REQSN_MST a WHERE a.ID=$update_id and a.ENTRY_FORM=357 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0");
	
	$working_company=$mst_sql[0]['WORKING_COMPANY_ID'];
	$working_location=$mst_sql[0]['WORKING_LOCATION_ID'];
	$floor=$mst_sql[0]['FLOOR_ID'];

	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$working_company' and a.location_id='$working_location' and a.floor_id='$floor' and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number");
	//echo $line_data;die;

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
	//print_r($line_array);
	
		
	?>
	<div style="width:1320px; margin-top:5px; padding-left:20px;">
		<table width="1320" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr style="margin-bottom:20px">
                <td colspan="8" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?></u></strong></center></td>
            </tr>
            <tr>
                <td width="160"><strong>System ID:</strong></td>
                <td width="160"><? echo $mst_sql[0]['REQ_NO']; ?></td>
                <td width="160"><strong>Working Company:</strong></td>
                <td width="160"><? echo $com_arr[$mst_sql[0]['WORKING_COMPANY_ID']]; ?></td>
                <td width="160"><strong>Location:</strong></td>
                <td width="160"><? echo $location_arr[$mst_sql[0]['WORKING_LOCATION_ID']]; ?></td>
                <td width="160"><strong>Garments Floor:</strong></td>
                <td><? echo $floor_arr[$mst_sql[0]['FLOOR_ID']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Sewing Line</strong></td>
				<td><? echo $line_array_new[$mst_sql[0]['SEWING_LINE']]; ?></td>
                <td><strong>Requisition Date:</strong></td>
                <td><? echo change_date_format($mst_sql[0]['REQUISITION_DATE']); ?></td>
                <td><strong>Delivery Date:</strong></td>
                <td><? echo change_date_format($mst_sql[0]['DELIVERY_DATE']); ?></td>
				<td><strong>Requisition Basis :</strong></td>
                <td><? echo $trim_type[$mst_sql[0]['TRIM_TYPE']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Remarks</strong></td>
				<td><? echo $mst_sql[0]['REMARKS']; ?></td>
            </tr>
        </table>
	    <table width="1320" cellspacing="0" align="right" border="0" style="margin-right:20px;"><tr><td colspan="8">&nbsp;</td></tr></table>
        <?
		
		$dtls_sql="SELECT a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, b.PO_ID, b.COLOR_SIZE_TABLE_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.JOB_NO, b.GMTS_COLOR_ID, b.CONS_UOM, b.CONS, b.REQSN_QTY, b.REMARKS
		from READY_TO_SEWING_REQSN_MST a, READY_TO_SEWING_REQSN b 
		WHERE a.id=b.mst_id and a.ID=$update_id and a.ENTRY_FORM=357 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and B.STATUS_ACTIVE=1 and B.IS_DELETED=0";
		//echo $dtls_sql;die;
		$dtls_sql_result=sql_select($dtls_sql);
		$job_check_arr=$all_gmst_color_arr=array();$all_job="";$all_po_id_arr=array();
		foreach($dtls_sql_result as $val)
		{
			if($job_check_arr[$val["JOB_NO"]]=="")
			{
				$job_check_arr[$val["JOB_NO"]]=$val["JOB_NO"];
				$all_job.="'".$val["JOB_NO"]."',";
			}
			$all_gmst_color_arr[$val["GMTS_COLOR_ID"]]=$val["GMTS_COLOR_ID"];
			$all_po_id_arr[$val["PO_ID"]]=$val["PO_ID"];
		}
		$all_job=chop($all_job,",");
		if($all_job!="")
		{
			
			$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, d.costing_per_id AS COSTING_PER 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d 
			where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.job_no in($all_job) and b.id in(".implode(",",$all_po_id_arr).")";
			//echo $sqlpo; die; //and a.job_no='$job_no'
			$sqlpoRes = sql_select($sqlpo);
			//print_r($sqlpoRes); die;
			$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
			foreach($sqlpoRes as $row)
			{
				$costingPerQty=0;
				if($row['COSTING_PER']==1) $costingPerQty=12;
				elseif($row['COSTING_PER']==2) $costingPerQty=1;	
				elseif($row['COSTING_PER']==3) $costingPerQty=24;
				elseif($row['COSTING_PER']==4) $costingPerQty=36;
				elseif($row['COSTING_PER']==5) $costingPerQty=48;
				else $costingPerQty=0;
				
				$costingPerArr[$row['JOB_ID']]=$costingPerQty;
				
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
				
				$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$job_id=$row['JOB_ID'];
			}
			unset($sqlpoRes);
			
			$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO 
			from wo_po_details_mas_set_details a
			where a.JOB_NO in($all_job)";
			//echo $gmtsitemRatioSql; die;
			$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
			$jobItemRatioArr=array();
			foreach($gmtsitemRatioSqlRes as $row)
			{
				$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			}
			unset($gmtsitemRatioSqlRes);
			
			$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts as CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
			from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
			where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.JOB_NO in($all_job)";
			//echo $sqlTrim; die;
			$sqlTrimRes = sql_select($sqlTrim);
			
			foreach($sqlTrimRes as $row)
			{
				$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
				
				$costingPer=$costingPerArr[$row['JOB_ID']];
				$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
				
				$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
				//print_r($poCountryId);
				
				if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
				{
					$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					
					$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
					$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
					
					$consAmt=$consQnty*$row['RATE'];
					$consTotAmt=$consTotQnty*$row['RATE'];
				}
				else
				{
					
					$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
					$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
					foreach($poCountryId as $countryId)
					{
						if(in_array($countryId, $countryIdArr))
						{
							$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
							$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
							$consQty=$consTotQty=0;
							
							$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
							//echo "2=".$poQty."=".$itemRatio."=".$row['CONS']."=".$costingPer;die;
							$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
							
							$consQnty+=$consQty;
							$consTotQnty+=$consTotQty;
							//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
							$consAmt+=$consQty*$row['RATE'];
							//$consTotAmt+=$consTotQty*$row['RATE'];
							$consTotAmt+=$consTotQnty*$row['RATE'];
						}
					}
				}
				//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
				$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['REQUIRED_QNTY']+=$consTotQnty;
				$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['CONS_DZN_GMTS']=$row['CONS_DZN_GMTS'];
				
			}
			//echo "<pre>";print_r($poReqDataArr);die;
			unset($sqlTrimRes);
			
			
			$prv_req_sql="SELECT a.ID as REQ_ID, b.PO_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.GMTS_COLOR_ID, b.REQSN_QTY 
			from ready_to_sewing_reqsn_mst a, ready_to_sewing_reqsn b 
			where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 and b.JOB_NO in($all_job) and b.GMTS_COLOR_ID in(".implode(",",$all_gmst_color_arr).") and a.id<>$update_id";
			// echo $prv_req_sql;die;
			$prv_req_result=sql_select($prv_req_sql);
			$pre_issue_data=array();
			if(count($prv_req_result)>0)
			{
				foreach($prv_req_result as $row)
				{
					$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']]+=$row['REQSN_QTY'];
				}
		
			}
			
			
			$cut_size_sql="select a.CUTTING_NO, a.JOB_NO, b.COLOR_ID, c.ORDER_ID, c.SIZE_ID, c.MARKER_QTY 
			from PPL_CUT_LAY_MST a, PPL_CUT_LAY_DTLS b, PPL_CUT_LAY_SIZE c 
			where a.id=b.mst_id and b.id=c.DTLS_ID and a.id=b.mst_id and a.JOB_NO in($all_job) and b.COLOR_ID in(".implode(",",$all_gmst_color_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			//echo $cut_size_sql;die;
			$cut_size_sql_result = sql_select($cut_size_sql);
			$size_data_arr=array();$size_wise_qnty_array=array();
			foreach($cut_size_sql_result as $val)
			{
				$size_data_arr[$val["SIZE_ID"]]=$size_arr[$val["SIZE_ID"]];
				$size_wise_qnty_array[$val["CUTTING_NO"]][$val["ORDER_ID"]][$val["COLOR_ID"]][$val["SIZE_ID"]]+=$val["MARKER_QTY"];
			}
			
			$po_sql="SELECT d.COMPANY_ID, d.CUTTING_NO as CUT_SEWING_NO, d.ENTRY_DATE as PLAN_DATE, e.ORDER_CUT_NO, a.ID as JOB_ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, b.ID as PO_ID, b.PO_NUMBER, e.COLOR_ID, e.ROLL_DATA
			from wo_po_details_master a, wo_po_break_down b, PPL_CUT_LAY_MST d, ppl_cut_lay_dtls e, PPL_CUT_LAY_SIZE f
			where a.id=b.job_id and b.JOB_NO_MST=d.JOB_NO and d.id=e.mst_id and e.ID=f.DTLS_ID and b.id=f.ORDER_ID and b.shiping_status!=3 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.JOB_NO in($all_job) and e.COLOR_ID in(".implode(",",$all_gmst_color_arr).") and b.id in(".implode(",",$all_po_id_arr).")
			group by d.COMPANY_ID, d.CUTTING_NO, d.ENTRY_DATE, e.ORDER_CUT_NO, a.ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, b.ID, b.PO_NUMBER, e.COLOR_ID, e.ROLL_DATA";
			
			
		}
		else
		{
			echo "No Details Data Found";die;
		}
		$result = sql_select($po_sql);
		$table_width=980+(count($size_data_arr)*50);
		?>
		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
                <th width="100">System Cut Number</th>
                <th width="50">Ord. Cut No.</th>
                <th width="80">Buyer</th>
                <th width="100">Style</th>
                <th width="100">Job</th>
                <th width="100">Country</th>
                <th width="110">Gmts. Item</th>
                <th width="100">PO</th>
                <th width="80">Color</th>
                <th width="110">Batch No.</th>
                <?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th width="50"><?= $size_val; ?></th>
					<?
				}
				?>
                <th width="90">Total</th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=0;$size_total_arr=array();
	        foreach($result as $row)
			{
				$roll_data_arr=explode("**",$row['ROLL_DATA']);
				$batch_no_arr=array();
				foreach($roll_data_arr as $roll_datas)
				{
					$roll_datas_all=explode("=",$roll_datas);
					if($roll_datas_all[5]!="") $batch_no_arr[$roll_datas_all[5]]=$roll_datas_all[5];
				}
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
		            <td><? echo $row['CUT_SEWING_NO']; ?></td>
                    <td><? echo $row['ORDER_CUT_NO']; ?></td>
		            <td><? echo $buyer_library[$row['BUYER_NAME']]; ?></td>
		            <td><? echo $row['STYLE_REF_NO']; ?></td>
                    <td><? echo $row['JOB_NO']; ?></td>
		            <td title="<?= $row['COUNTRY_ID'];?>"><? echo $country_library[$row['COUNTRY_ID']] ?></td>
                    <td title="<?= $row['GMT_ITEM_ID'];?>"><? echo $garments_item[$row['GMT_ITEM_ID']]; ?></td>
		            <td><? echo $row['PO_NUMBER']; ?></td>
		            <td title="<?= $row['COLOR_ID'];?>"><? echo $color_arr[$row['COLOR_ID']]; ?></td>
                    <td><? echo implode(",",$batch_no_arr); ?></td>
                    <?
					$row_total=0;
					foreach($size_data_arr as $size_id=>$size_val)
					{
						?>
						<td align="right"><?= number_format($size_wise_qnty_array[$row["CUT_SEWING_NO"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id],0); ?></td>
						<?
						$row_total+=$size_wise_qnty_array[$row["CUT_SEWING_NO"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
						$size_total_arr[$size_id]+=$size_wise_qnty_array[$row["CUT_SEWING_NO"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
					}
					?>
                    <td align="right"><?= number_format($row_total,0); $tot_qnty+=$row_total; ?></td>
				</tr>
				<?
				$i++;
			}
	        ?>
	        </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Total:</th>
                <?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th><?= $size_total_arr[$size_id]; $gt_tot+=$size_total_arr[$size_id]; ?></th>
					<?
				}
				?>
                <th><?= $gt_tot;?></th>
            </tfoot>
	    </table>
        
	    <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" width="1000" style="margin-top:30px;">
	        <thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="120">Name Of Item</th>
					<th width="120">Description</th>
					<th width="80">Consumption/DZN</th>
					<th width="60">UOM</th>
					<th width="80">Required Qty</th>
					<th width="80">Booking Qty</th>
					<th width="80">Cumu. Requisition Qty</th>
					<th width="80">Current Requisition Qty</th>
					<th width="100">TTL Requisition Qty</th>
					<th width="80">Requ. Balance</th>
                    <th>Remarks</th>
				</tr>
	        </thead>
	        <tbody>
			<?
			// var_dump($all_data_info);
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($dtls_sql_result as $row)
			{
				$required_qnty=$poReqDataArr[$row['PRECOST_TRIM_DTLS_ID']][$row['PO_ID']][$row['GMTS_COLOR_ID']]['REQUIRED_QNTY'];
				$cons_dzn_gmts=$poReqDataArr[$row['PRECOST_TRIM_DTLS_ID']][$row['PO_ID']][$row['GMTS_COLOR_ID']]['CONS_DZN_GMTS'];
				$prev_req_qnty=$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']];
				$total_req_qnty=$prev_req_qnty+$row['REQSN_QTY'];
				$balance_qnty=$required_qnty-$total_req_qnty;
				if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $item_group_arr[$row['TRIM_GROUP']]; ?></td>
					<td><? echo $row['ITEM_DESCRIPTION']; ?></td>
					<td align="right"><? echo $cons_dzn_gmts; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row['CONS_UOM']]; ?></td>
					<td align="right"><? echo number_format($required_qnty,4);?></td>
					<td align="right"><? echo number_format($row['CONS'],4);?></td>
					<td align="right"><? echo number_format($prev_req_qnty,4);?></td>
					<td align="right"><? echo number_format($row['REQSN_QTY'],4);?></td>
                    <td align="right"><? echo number_format($total_req_qnty,4);?></td>
                    <td align="right"><? echo number_format($balance_qnty,4);?></td>
					<td><? echo $row['REMARKS']; ?></td>
				</tr>
				<?
				$i++;
			}
	        ?>
	        </tbody>
	    </table>
		</div>
		 <?
            echo signature_table(320, $data[0], $table_width."px");
         ?>
	</div>
	<?
	exit();
}


if($action=="garments_exfactory_print3")
{
	//$start = microtime(true);
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[1];
	$update_id=$data[2];
	echo load_html_head_contents("Trims Issue Requisition V2","../", 1, 1, $unicode,'','');
	//$com_dtls = fnc_company_location_address($company, $location, 2);
	$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where item_category=4",'id','item_name');
	$com_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$country_library=return_library_array( "select id, country_name from lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	
	
	
	$mst_sql=sql_select("SELECT a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, a.REMARKS
	from READY_TO_SEWING_REQSN_MST a WHERE a.ID=$update_id and a.ENTRY_FORM=357 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0");
	
	$working_company=$mst_sql[0]['WORKING_COMPANY_ID'];
	$working_location=$mst_sql[0]['WORKING_LOCATION_ID'];
	$floor=$mst_sql[0]['FLOOR_ID'];
	
		
	?>
	<div style="width:1320px; margin-top:5px; padding-left:20px;">
		<table width="1320" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr style="margin-bottom:20px">
                <td colspan="8" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?></u></strong></center></td>
            </tr>
            <tr>
                <td width="160"><strong>System ID:</strong></td>
                <td width="160"><? echo $mst_sql[0]['REQ_NO']; ?></td>
                <td width="160"><strong>Working Company:</strong></td>
                <td width="160"><? echo $com_arr[$mst_sql[0]['WORKING_COMPANY_ID']]; ?></td>
                <td width="160"><strong>Location:</strong></td>
                <td width="160"><? echo $location_arr[$mst_sql[0]['WORKING_LOCATION_ID']]; ?></td>
                <td width="160"><strong>Garments Floor:</strong></td>
                <td><? echo $floor_arr[$mst_sql[0]['FLOOR_ID']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Sewing Line</strong></td>
				<td><? echo $line_array_new[$mst_sql[0]['SEWING_LINE']]; ?></td>
                <td><strong>Requisition Date:</strong></td>
                <td><? echo change_date_format($mst_sql[0]['REQUISITION_DATE']); ?></td>
                <td><strong>Delivery Date:</strong></td>
                <td><? echo change_date_format($mst_sql[0]['DELIVERY_DATE']); ?></td>
				<td><strong>Requisition Basis :</strong></td>
                <td><? echo $trim_type[$mst_sql[0]['TRIM_TYPE']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Remarks</strong></td>
				<td><? echo $mst_sql[0]['REMARKS']; ?></td>
            </tr>
        </table>
	    <table width="1320" cellspacing="0" align="right" border="0" style="margin-right:20px;"><tr><td colspan="8">&nbsp;</td></tr></table>
        <?
		
		$dtls_sql="SELECT a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, b.PO_ID, b.COLOR_SIZE_TABLE_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.JOB_NO, b.GMTS_COLOR_ID, b.CONS_UOM, b.CONS, b.REQSN_QTY, b.REMARKS, c.ITEM_COLOR, b.SIZE_INPUT_QTY as STORE_RCV_QNTY, b.PRODUCT_ID
		from READY_TO_SEWING_REQSN_MST a, READY_TO_SEWING_REQSN b, PRODUCT_DETAILS_MASTER c 
		WHERE a.id=b.mst_id and b.PRODUCT_ID=c.id and a.ID=$update_id and a.ENTRY_FORM=357 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and B.STATUS_ACTIVE=1 and B.IS_DELETED=0";
		//echo $dtls_sql;die;
		$dtls_sql_result=sql_select($dtls_sql);
		$job_check_arr=$all_gmst_color_arr=array();$all_job="";$all_po_id_arr=array();
		foreach($dtls_sql_result as $val)
		{
			if($job_check_arr[$val["JOB_NO"]]=="")
			{
				$job_check_arr[$val["JOB_NO"]]=$val["JOB_NO"];
				$all_job.="'".$val["JOB_NO"]."',";
			}
			$all_gmst_color_arr[$val["GMTS_COLOR_ID"]]=$val["GMTS_COLOR_ID"];
			$all_po_id_arr[$val["PO_ID"]]=$val["PO_ID"];
		}
		$all_job=chop($all_job,",");
		if($all_job!="")
		{
			
			$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, d.costing_per_id AS COSTING_PER 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d 
			where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.job_no in($all_job) and b.id in(".implode(",",$all_po_id_arr).")";
			//echo $sqlpo; die; //and a.job_no='$job_no'
			$sqlpoRes = sql_select($sqlpo);
			//print_r($sqlpoRes); die;
			$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
			foreach($sqlpoRes as $row)
			{
				$costingPerQty=0;
				if($row['COSTING_PER']==1) $costingPerQty=12;
				elseif($row['COSTING_PER']==2) $costingPerQty=1;	
				elseif($row['COSTING_PER']==3) $costingPerQty=24;
				elseif($row['COSTING_PER']==4) $costingPerQty=36;
				elseif($row['COSTING_PER']==5) $costingPerQty=48;
				else $costingPerQty=0;
				
				$costingPerArr[$row['JOB_ID']]=$costingPerQty;
				
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
				
				$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$job_id=$row['JOB_ID'];
			}
			unset($sqlpoRes);
			
			$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO 
			from wo_po_details_mas_set_details a
			where a.JOB_NO in($all_job)";
			//echo $gmtsitemRatioSql; die;
			$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
			$jobItemRatioArr=array();
			foreach($gmtsitemRatioSqlRes as $row)
			{
				$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			}
			unset($gmtsitemRatioSqlRes);
			
			$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts as CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
			from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
			where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.JOB_NO in($all_job)";
			//echo $sqlTrim; die;
			$sqlTrimRes = sql_select($sqlTrim);
			
			foreach($sqlTrimRes as $row)
			{
				$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
				
				$costingPer=$costingPerArr[$row['JOB_ID']];
				$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
				
				$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
				//print_r($poCountryId);
				
				if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
				{
					$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					
					$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
					$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
					
					$consAmt=$consQnty*$row['RATE'];
					$consTotAmt=$consTotQnty*$row['RATE'];
				}
				else
				{
					
					$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
					$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
					foreach($poCountryId as $countryId)
					{
						if(in_array($countryId, $countryIdArr))
						{
							$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
							$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
							$consQty=$consTotQty=0;
							
							$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
							//echo "2=".$poQty."=".$itemRatio."=".$row['CONS']."=".$costingPer;die;
							$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
							
							$consQnty+=$consQty;
							$consTotQnty+=$consTotQty;
							//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
							$consAmt+=$consQty*$row['RATE'];
							//$consTotAmt+=$consTotQty*$row['RATE'];
							$consTotAmt+=$consTotQnty*$row['RATE'];
						}
					}
				}
				//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
				$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['REQUIRED_QNTY']+=$consTotQnty;
				$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['CONS_DZN_GMTS']=$row['CONS_DZN_GMTS'];
				
			}
			//echo "<pre>";print_r($poReqDataArr);die;
			unset($sqlTrimRes);
			
			
			$prv_req_sql="SELECT a.ID as REQ_ID, b.PO_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.GMTS_COLOR_ID, b.REQSN_QTY 
			from ready_to_sewing_reqsn_mst a, ready_to_sewing_reqsn b 
			where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 and b.JOB_NO in($all_job) and b.GMTS_COLOR_ID in(".implode(",",$all_gmst_color_arr).") and a.id<>$update_id";
			// echo $prv_req_sql;die;
			$prv_req_result=sql_select($prv_req_sql);
			$pre_issue_data=array();
			if(count($prv_req_result)>0)
			{
				foreach($prv_req_result as $row)
				{
					$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']]+=$row['REQSN_QTY'];
				}
		
			}
			
			
			$cut_size_sql="select a.CUTTING_NO, a.JOB_NO, b.COLOR_ID, c.ORDER_ID, c.SIZE_ID, c.MARKER_QTY 
			from PPL_CUT_LAY_MST a, PPL_CUT_LAY_DTLS b, PPL_CUT_LAY_SIZE c 
			where a.id=b.mst_id and b.id=c.DTLS_ID and a.id=b.mst_id and a.JOB_NO in($all_job) and b.COLOR_ID in(".implode(",",$all_gmst_color_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			//echo $cut_size_sql;die;
			$cut_size_sql_result = sql_select($cut_size_sql);
			$size_data_arr=array();$size_wise_qnty_array=array();
			foreach($cut_size_sql_result as $val)
			{
				$size_data_arr[$val["SIZE_ID"]]=$size_arr[$val["SIZE_ID"]];
				$size_wise_qnty_array[$val["CUTTING_NO"]][$val["ORDER_ID"]][$val["COLOR_ID"]][$val["SIZE_ID"]]+=$val["MARKER_QTY"];
			}
			
			$po_sql="SELECT d.COMPANY_ID, d.CUTTING_NO as CUT_SEWING_NO, d.ENTRY_DATE as PLAN_DATE, e.ORDER_CUT_NO, a.ID as JOB_ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, b.ID as PO_ID, b.PO_NUMBER, e.COLOR_ID, e.ROLL_DATA
			from wo_po_details_master a, wo_po_break_down b, PPL_CUT_LAY_MST d, ppl_cut_lay_dtls e, PPL_CUT_LAY_SIZE f
			where a.id=b.job_id and b.JOB_NO_MST=d.JOB_NO and d.id=e.mst_id and e.ID=f.DTLS_ID and b.id=f.ORDER_ID and b.shiping_status!=3 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.JOB_NO in($all_job) and e.COLOR_ID in(".implode(",",$all_gmst_color_arr).") and b.id in(".implode(",",$all_po_id_arr).")
			group by d.COMPANY_ID, d.CUTTING_NO, d.ENTRY_DATE, e.ORDER_CUT_NO, a.ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, b.ID, b.PO_NUMBER, e.COLOR_ID, e.ROLL_DATA";
			
			$cut_size_sql="select c.SYS_NUMBER as CUTTING_NO, a.JOB_NO_MST as JOB_NO, a.COLOR_NUMBER_ID as COLOR_ID, a.PO_BREAK_DOWN_ID as ORDER_ID, a.SIZE_NUMBER_ID as SIZE_ID, b.PRODUCTION_QNTY as MARKER_QTY 
			from WO_PO_COLOR_SIZE_BREAKDOWN a, PRO_GARMENTS_PRODUCTION_DTLS b, PRO_GMTS_DELIVERY_MST c 
			where a.id=b.COLOR_SIZE_BREAK_DOWN_ID and b.DELIVERY_MST_ID=c.ID and a.JOB_NO_MST  in($all_job) and a.COLOR_NUMBER_ID in(".implode(",",$all_gmst_color_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			//echo $cut_size_sql;
			$cut_size_sql_result = sql_select($cut_size_sql);
			$size_data_arr=array();$size_wise_qnty_array=array();
			foreach($cut_size_sql_result as $val)
			{
				$size_data_arr[$val["SIZE_ID"]]=$size_arr[$val["SIZE_ID"]];
				$size_wise_qnty_array[$val["CUTTING_NO"]][$val["ORDER_ID"]][$val["COLOR_ID"]][$val["SIZE_ID"]]+=$val["MARKER_QTY"];
			}
			
			$floor_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5",'id','floor_name');
			$nameArray = sql_select("select id, auto_update from variable_settings_production where company_name=$working_company and variable_list=23 and status_active=1 and is_deleted=0");
			$prod_reso_allocation = $nameArray[0][csf('auto_update')];
			//echo $prod_reso_allocation.test;die;
			if ($prod_reso_allocation == 1) 
			{
				$line_library = return_library_array("select id,line_name,sewing_line_serial from lib_sewing_line where status_active=1 order by sewing_line_serial", "id", "line_name");
				$line_array = array();
		
				$line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
		
		
				$line_merge=9999;
				foreach($line_data as $row)
				{
					$line='';
					$line_number=explode(",",$row[csf('line_number')]);
					foreach($line_number as $val)
					{
						if(count($line_number)>1)
						{
							$line_merge++;
							$new_arr[$line_merge]=$row[csf('id')];
						}
						else
						{
							if($new_arr[$line_library[$val]])
							$new_arr[$line_library[$val]." "]=$row[csf('id')];
							else
								$new_arr[$line_library[$val]]=$row[csf('id')];
						}
		
						if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
					}
					$line_array[$row[csf('id')]]=$line;
				}
				//ksort($new_arr);
				foreach($new_arr as $key=>$v)
				{
					$line_array_new[$v]=$line_array[$v];
				}
		
			} 
			else 
			{
				$line_array_new = return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1",'id','line_name');
			}
			
			$po_sql="SELECT a.COMPANY_NAME as COMPANY_ID, d.CUT_NO as CUT_SEWING_NO, a.ID as JOB_ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, c.ITEM_NUMBER_ID as GMT_ITEM_ID, b.ID as PO_ID, b.PO_NUMBER, c.COLOR_NUMBER_ID as COLOR_ID, e.SYS_NUMBER, e.FLOOR_ID, e.SEWING_LINE, e.DELIVERY_DATE
			from wo_po_details_master a, wo_po_break_down b, WO_PO_COLOR_SIZE_BREAKDOWN c, PRO_GARMENTS_PRODUCTION_DTLS d, pro_gmts_delivery_mst e
			where a.id=b.job_id and b.id=c.PO_BREAK_DOWN_ID and c.id=d.COLOR_SIZE_BREAK_DOWN_ID and d.DELIVERY_MST_ID=e.id and d.PRODUCTION_TYPE=4 and a.JOB_NO  in($all_job) and c.COLOR_NUMBER_ID in(".implode(",",$all_gmst_color_arr).") and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
			group by a.COMPANY_NAME, d.CUT_NO, a.ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, c.ITEM_NUMBER_ID, b.ID, b.PO_NUMBER, c.COLOR_NUMBER_ID, e.SYS_NUMBER, e.FLOOR_ID, e.SEWING_LINE, e.DELIVERY_DATE";
			
			
		}
		else
		{
			echo "No Details Data Found";die;
		}
		$result = sql_select($po_sql);
		$table_width=1100+(count($size_data_arr)*50);
		?>
		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead>
			<tr>
				<th width="120">Company</th>
				<th width="90">System Cut No</th>
				<th width="90">Challan No</th>
				<th width="80">Buyer</th>
				<th width="90">Style</th>
				<th width="90">Job</th>
				<th width="120">Gmts. Item</th>
				<th width="100">PO</th>
				<th width="100">Color</th>
				<th width="80">Floor</th>
                <th width="80">Line</th>
				<?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th width="50"><?= $size_val; ?></th>
					<?
				}
				?>
				<th>Total</th>
			</tr>
		</thead>
            <tbody>
            <?
            $i=1;$size_wise_total=array();
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" <? if($generate_level!=2) { ?> onClick="fn_item_details('<? echo $row['PO_ID']; ?>','<? echo $gmst_color_id; ?>','0','0');" <? } ?> > 
                    <td><? echo $com_arr[$row['COMPANY_ID']]; ?></td>
                    <td><? echo $row['CUT_SEWING_NO']; ?></td>
                    <td><? echo $row['SYS_NUMBER']; ?></td>
                    <td><? echo $buyer_library[$row['BUYER_NAME']]; ?></td>
                    <td><? echo $row['STYLE_REF_NO']; ?></td>
                    <td><? echo $row['JOB_NO'] ?></td>
                    <td><? echo $garments_item[$row['GMT_ITEM_ID']]; ?></td>				
                    <td><? echo $row['PO_NUMBER'] ?></td>
                    <td><? echo $color_arr[$row['COLOR_ID']]; ?></td>
                    <td><? echo $floor_arr[$row['FLOOR_ID']]; ?></td>
                    <td><? echo $line_array_new[$row['SEWING_LINE']]; ?></td>
                    <?
                    $row_tot_size_qnty=0;
                    foreach($size_data_arr as $size_id=>$size_val)
                    {
                        ?>
                        <td align="right"><?= $size_wise_qnty_array[$row["SYS_NUMBER"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id]; ?></td>
                        <?
                        $row_tot_size_qnty+=$size_wise_qnty_array[$row["SYS_NUMBER"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
                        $size_wise_total[$size_id]+=$size_wise_qnty_array[$row["SYS_NUMBER"]][$row["PO_ID"]][$row["COLOR_ID"]][$size_id];
                    }
                    ?>
                    <td align="right"><? echo $row_tot_size_qnty; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total:</th>
                    <?
                    foreach($size_data_arr as $size_id=>$size_val)
                    {
                        ?>
                        <th align="right"><?= $size_wise_total[$size_id]; ?></th>
                        <?
                        $grand_total+=$size_wise_total[$size_id];
                    }
                    ?>
                    <th align="right"><?= $grand_total; ?></th>
                </tr>
            </tfoot>
	    </table>
        
	    <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" width="1250" style="margin-top:30px;">
	        <thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="50">Product ID</th>
                    <th width="120">Item Group</th>
					<th width="120">Item Description</th>
                    <th width="80">Item Color</th>
                    <th width="80">Gmts. Color</th>
					<th width="80">Consumption /DZN</th>
					<th width="60">UOM</th>
					<th width="80">Required Qty</th>
					<th width="80">Booking Qty</th>
                    <th width="80">Store Rcv Qnty</th>
					<th width="80">Cumu. Requisition Qty</th>
					<th width="80">Current Requisition Qty</th>
					<th width="80">TTL Requisition Qty</th>
					<th width="80">Requ. Balance</th>
                    
                    <th>Remarks</th>
				</tr>
	        </thead>
	        <tbody>
			<?
			// var_dump($all_data_info);
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($dtls_sql_result as $row)
			{
				$required_qnty=$poReqDataArr[$row['PRECOST_TRIM_DTLS_ID']][$row['PO_ID']][$row['GMTS_COLOR_ID']]['REQUIRED_QNTY'];
				$cons_dzn_gmts=$poReqDataArr[$row['PRECOST_TRIM_DTLS_ID']][$row['PO_ID']][$row['GMTS_COLOR_ID']]['CONS_DZN_GMTS'];
				$prev_req_qnty=$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']];
				$total_req_qnty=$prev_req_qnty+$row['REQSN_QTY'];
				$balance_qnty=$row['STORE_RCV_QNTY']-$total_req_qnty;
				if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $row['PRODUCT_ID']; ?></td>
                    <td><? echo $item_group_arr[$row['TRIM_GROUP']]; ?></td>
					<td><? echo $row['ITEM_DESCRIPTION']; ?></td>
                    <td><? echo $color_arr[$row['ITEM_COLOR']]; ?></td>
                    <td><? echo $color_arr[$row['GMTS_COLOR_ID']]; ?></td>
					<td align="right"><? echo $cons_dzn_gmts; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row['CONS_UOM']]; ?></td>
					<td align="right"><? echo number_format($required_qnty,4);?></td>
					<td align="right"><? echo number_format($row['CONS'],4);?></td>
                    <td align="right"><? echo number_format($row['STORE_RCV_QNTY'],4);?></td>
					<td align="right"><? echo number_format($prev_req_qnty,4);?></td>
					<td align="right"><? echo number_format($row['REQSN_QTY'],4);?></td>
                    <td align="right"><? echo number_format($total_req_qnty,4);?></td>
                    <td align="right"><? echo number_format($balance_qnty,4);?></td>
					<td><? echo $row['REMARKS']; ?></td>
				</tr>
				<?
				$i++;
			}
	        ?>
	        </tbody>
	    </table>
		</div>
		 <?
            echo signature_table(320, $data[0], $table_width."px");
         ?>
	</div>
	<?
	exit();
}

if($action=="garments_exfactory_print_job")
{
	//$start = microtime(true);
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[1];
	$update_id=$data[2];
	echo load_html_head_contents("Trims Issue Requisition V2","../", 1, 1, $unicode,'','');
	//$com_dtls = fnc_company_location_address($company, $location, 2);
	$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group where item_category=4",'id','item_name');
	$com_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor where production_process=5 and status_active=1 and is_deleted=0", "id", "floor_name");
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$country_library=return_library_array( "select id, country_name from lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$company' and a.location_id='$location' and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number");
	//echo $line_data;die;

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
	
	$mst_sql=sql_select("SELECT a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, a.REMARKS
	from READY_TO_SEWING_REQSN_MST a WHERE a.ID=$update_id and a.ENTRY_FORM=357 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0");
	
	$working_company=$mst_sql[0]['WORKING_COMPANY_ID'];
	$working_location=$mst_sql[0]['WORKING_LOCATION_ID'];
	$floor=$mst_sql[0]['FLOOR_ID'];

	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$working_company' and a.location_id='$working_location' and a.floor_id='$floor' and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number");
	//echo $line_data;die;

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
	//print_r($line_array);
	
		
	?>
	<div style="width:1320px; margin-top:5px; padding-left:20px;"">
		<table width="1320" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr style="margin-bottom:20px">
                <td colspan="8" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?></u></strong></center></td>
            </tr>
            <tr>
                <td width="160"><strong>System ID:</strong></td>
                <td width="160"><? echo $mst_sql[0]['REQ_NO']; ?></td>
                <td width="160"><strong>Working Company:</strong></td>
                <td width="160"><? echo $com_arr[$mst_sql[0]['WORKING_COMPANY_ID']]; ?></td>
                <td width="160"><strong>Location:</strong></td>
                <td width="160"><? echo $location_arr[$mst_sql[0]['WORKING_LOCATION_ID']]; ?></td>
                <td width="160"><strong>Garments Floor:</strong></td>
                <td><? echo $floor_arr[$mst_sql[0]['FLOOR_ID']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Sewing Line</strong></td>
				<td><? echo $line_array_new[$mst_sql[0]['SEWING_LINE']]; ?></td>
                <td><strong>Requisition Date:</strong></td>
                <td><? echo change_date_format($mst_sql[0]['REQUISITION_DATE']); ?></td>
                <td><strong>Delivery Date:</strong></td>
                <td><? echo change_date_format($mst_sql[0]['DELIVERY_DATE']); ?></td>
				<td><strong>Requisition Basis :</strong></td>
                <td><? echo $trim_type[$mst_sql[0]['TRIM_TYPE']]; ?></td>
            </tr>
            <tr>
            	<td><strong>Remarks</strong></td>
				<td><? echo $mst_sql[0]['REMARKS']; ?></td>
            </tr>
        </table>
	    <table width="1320" cellspacing="0" align="right" border="0" style="margin-right:20px;"><tr><td colspan="8">&nbsp;</td></tr></table>
        <?
		
		$dtls_sql="SELECT a.ID, a.REQ_NO, a.WORKING_COMPANY_ID, a.WORKING_LOCATION_ID, a.FLOOR_ID, a.SEWING_LINE, a.REQUISITION_DATE, a.DELIVERY_DATE, a.TRIM_TYPE, b.PO_ID, b.COLOR_SIZE_TABLE_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.JOB_NO, b.GMTS_COLOR_ID, b.CONS_UOM, b.CONS, b.REQSN_QTY, b.REMARKS
		from READY_TO_SEWING_REQSN_MST a, READY_TO_SEWING_REQSN b 
		WHERE a.id=b.mst_id and a.ID=$update_id and a.ENTRY_FORM=357 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and B.STATUS_ACTIVE=1 and B.IS_DELETED=0";
		//echo $dtls_sql;die;
		$dtls_sql_result=sql_select($dtls_sql);
		$job_check_arr=$all_gmst_color_arr=array();$all_job="";$all_po_id_arr=array();
		foreach($dtls_sql_result as $val)
		{
			if($job_check_arr[$val["JOB_NO"]]=="")
			{
				$job_check_arr[$val["JOB_NO"]]=$val["JOB_NO"];
				$all_job.="'".$val["JOB_NO"]."',";
			}
			$all_gmst_color_arr[$val["GMTS_COLOR_ID"]]=$val["GMTS_COLOR_ID"];
			$all_po_id_arr[$val["PO_ID"]]=$val["PO_ID"];
		}
		$all_job=chop($all_job,",");
		if($all_job!="")
		{
			
			$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, d.costing_per_id AS COSTING_PER 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d 
			where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.job_no in($all_job) and b.id in(".implode(",",$all_po_id_arr).")";
			//echo $sqlpo; die; //and a.job_no='$job_no'
			$sqlpoRes = sql_select($sqlpo);
			//print_r($sqlpoRes); die;
			$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
			foreach($sqlpoRes as $row)
			{
				$costingPerQty=0;
				if($row['COSTING_PER']==1) $costingPerQty=12;
				elseif($row['COSTING_PER']==2) $costingPerQty=1;	
				elseif($row['COSTING_PER']==3) $costingPerQty=24;
				elseif($row['COSTING_PER']==4) $costingPerQty=36;
				elseif($row['COSTING_PER']==5) $costingPerQty=48;
				else $costingPerQty=0;
				
				$costingPerArr[$row['JOB_ID']]=$costingPerQty;
				
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
				
				$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
				$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
				
				$job_id=$row['JOB_ID'];
			}
			unset($sqlpoRes);
			
			$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO 
			from wo_po_details_mas_set_details a
			where a.JOB_NO in($all_job)";
			//echo $gmtsitemRatioSql; die;
			$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
			$jobItemRatioArr=array();
			foreach($gmtsitemRatioSqlRes as $row)
			{
				$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			}
			unset($gmtsitemRatioSqlRes);
			
			$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts as CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
			from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
			where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.JOB_NO in($all_job)";
			//echo $sqlTrim; die;
			$sqlTrimRes = sql_select($sqlTrim);
			
			foreach($sqlTrimRes as $row)
			{
				$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
				
				$costingPer=$costingPerArr[$row['JOB_ID']];
				$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
				
				$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
				//print_r($poCountryId);
				
				if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
				{
					$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					
					$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
					$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
					
					$consAmt=$consQnty*$row['RATE'];
					$consTotAmt=$consTotQnty*$row['RATE'];
				}
				else
				{
					
					$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
					$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
					foreach($poCountryId as $countryId)
					{
						if(in_array($countryId, $countryIdArr))
						{
							$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
							$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
							$consQty=$consTotQty=0;
							
							$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
							//echo "2=".$poQty."=".$itemRatio."=".$row['CONS']."=".$costingPer;die;
							$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
							
							$consQnty+=$consQty;
							$consTotQnty+=$consTotQty;
							//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
							$consAmt+=$consQty*$row['RATE'];
							//$consTotAmt+=$consTotQty*$row['RATE'];
							$consTotAmt+=$consTotQnty*$row['RATE'];
						}
					}
				}
				//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
				$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['REQUIRED_QNTY']+=$consTotQnty;
				$poReqDataArr[$row['TRIMID']][$row['POID']][$row['COLOR_NUMBER_ID']]['CONS_DZN_GMTS']=$row['CONS_DZN_GMTS'];
				
			}
			//echo "<pre>";print_r($poReqDataArr);die;
			unset($sqlTrimRes);
			
			
			$prv_req_sql="SELECT a.ID as REQ_ID, b.PO_ID, b.PRECOST_TRIM_DTLS_ID, b.TRIM_GROUP, b.ITEM_DESCRIPTION, b.GMTS_COLOR_ID, b.REQSN_QTY 
			from ready_to_sewing_reqsn_mst a, ready_to_sewing_reqsn b 
			where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 and b.JOB_NO in($all_job) and b.GMTS_COLOR_ID in(".implode(",",$all_gmst_color_arr).") and a.id<>$update_id";
			// echo $prv_req_sql;die;
			$prv_req_result=sql_select($prv_req_sql);
			$pre_issue_data=array();
			if(count($prv_req_result)>0)
			{
				foreach($prv_req_result as $row)
				{
					$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']]+=$row['REQSN_QTY'];
				}
		
			}
			
			
			$cut_size_sql="select a.JOB_NO, b.COLOR_ID, c.ORDER_ID, c.SIZE_ID, c.MARKER_QTY 
			from PPL_CUT_LAY_MST a, PPL_CUT_LAY_DTLS b, PPL_CUT_LAY_SIZE c 
			where a.id=b.mst_id and b.id=c.DTLS_ID and a.id=b.mst_id and a.JOB_NO in($all_job) and b.COLOR_ID in(".implode(",",$all_gmst_color_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			//echo $cut_size_sql;die;
			$cut_size_sql_result = sql_select($cut_size_sql);
			$size_data_arr=array();$size_wise_qnty_array=array();
			foreach($cut_size_sql_result as $val)
			{
				$size_data_arr[$val["SIZE_ID"]]=$size_arr[$val["SIZE_ID"]];
				$size_wise_qnty_array[$val["JOB_NO"]][$val["COLOR_ID"]][$val["SIZE_ID"]]+=$val["MARKER_QTY"];
			}
			
			$po_sql="SELECT d.COMPANY_ID, d.CUTTING_NO as CUT_SEWING_NO, d.ENTRY_DATE as PLAN_DATE, e.ORDER_CUT_NO, a.ID as JOB_ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, e.COLOR_ID, e.ROLL_DATA
			from wo_po_details_master a, wo_po_break_down b, PPL_CUT_LAY_MST d, ppl_cut_lay_dtls e, PPL_CUT_LAY_SIZE f
			where a.id=b.job_id and b.JOB_NO_MST=d.JOB_NO and d.id=e.mst_id and e.ID=f.DTLS_ID and b.id=f.ORDER_ID and b.shiping_status!=3 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.JOB_NO in($all_job) and e.COLOR_ID in(".implode(",",$all_gmst_color_arr).") and b.id in(".implode(",",$all_po_id_arr).")
			group by d.COMPANY_ID, d.CUTTING_NO, d.ENTRY_DATE, e.ORDER_CUT_NO, a.ID, a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, e.GMT_ITEM_ID, f.COUNTRY_ID, e.COLOR_ID, e.ROLL_DATA";
			
			
		}
		else
		{
			echo "No Details Data Found";die;
		}
		$result = sql_select($po_sql);
		$table_width=880+(count($size_data_arr)*70);
		?>
		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
                <th width="100">System Cut Number</th>
                <th width="50">Ord. Cut No.</th>
                <th width="80">Buyer</th>
                <th width="100">Style</th>
                <th width="100">Job</th>
                <th width="100">Country</th>
                <th width="110">Gmts. Item</th>
                <th width="80">Color</th>
                <th width="110">Batch No.</th>
                <?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th width="70"><?= $size_val; ?></th>
					<?
				}
				?>
                <th width="90">Total</th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=0;$size_total_arr=array();$batch_wise_qnty_arr=array();
	        foreach($result as $row)
			{
				$roll_data_arr=explode("**",$row['ROLL_DATA']);
				$batch_no_arr=array();
				foreach($roll_data_arr as $roll_datas)
				{
					$roll_datas_all=explode("=",$roll_datas);
					if($roll_datas_all[5]!="") $batch_no_arr[$roll_datas_all[5]]=$roll_datas_all[5];
				}
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
		            <td><? echo $row['CUT_SEWING_NO']; ?></td>
                    <td><? echo $row['ORDER_CUT_NO']; ?></td>
		            <td><? echo $buyer_library[$row['BUYER_NAME']]; ?></td>
		            <td><? echo $row['STYLE_REF_NO']; ?></td>
                    <td><? echo $row['JOB_NO']; ?></td>
		            <td title="<?= $row['COUNTRY_ID'];?>"><? echo $country_library[$row['COUNTRY_ID']] ?></td>
                    <td title="<?= $row['GMT_ITEM_ID'];?>"><? echo $garments_item[$row['GMT_ITEM_ID']]; ?></td>
		            <td title="<?= $row['COLOR_ID'];?>"><? echo $color_arr[$row['COLOR_ID']]; ?></td>
                    <td><? echo implode(",",$batch_no_arr); ?></td>
                    <?
					$row_total=0;
					foreach($size_data_arr as $size_id=>$size_val)
					{
						?>
						<td align="right"><?= number_format($size_wise_qnty_array[$row["JOB_NO"]][$row["COLOR_ID"]][$size_id],0); ?></td>
						<?
						$row_total+=$size_wise_qnty_array[$row["JOB_NO"]][$row["COLOR_ID"]][$size_id];
						$size_total_arr[$size_id]+=$size_wise_qnty_array[$row["JOB_NO"]][$row["COLOR_ID"]][$size_id];
					}
					?>
                    <td align="right"><?= number_format($row_total,0); $tot_qnty+=$row_total; ?></td>
				</tr>
				<?
				$i++;
				$batch_wise_qnty_arr[implode(",",$batch_no_arr)]["qnty"]=$row_total;
			}
	        ?>
	        </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Total:</th>
                <?
				foreach($size_data_arr as $size_id=>$size_val)
				{
					?>
					<th><?= $size_total_arr[$size_id]; $gt_tot+=$size_total_arr[$size_id]; ?></th>
					<?
				}
				?>
                <th><?= $gt_tot;?></th>
            </tfoot>
	    </table>
        
	    <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" width="1000" style="margin-top:30px;">
	        <thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="120">Name Of Item</th>
					<th width="120">Description</th>
					<th width="80">Consumption/DZN</th>
					<th width="60">UOM</th>
					<th width="80">Required Qty</th>
					<th width="80">Booking Qty</th>
					<th width="80">Cumu. Requisition Qty</th>
					<th width="80">Current Requisition Qty</th>
					<th width="100">TTL Requisition Qty</th>
					<th width="80">Requ. Balance</th>
                    <th>Remarks</th>
				</tr>
	        </thead>
	        <tbody>
			<?
			// var_dump($all_data_info);
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($dtls_sql_result as $row)
			{
				$required_qnty=$poReqDataArr[$row['PRECOST_TRIM_DTLS_ID']][$row['PO_ID']][$row['GMTS_COLOR_ID']]['REQUIRED_QNTY'];
				$cons_dzn_gmts=$poReqDataArr[$row['PRECOST_TRIM_DTLS_ID']][$row['PO_ID']][$row['GMTS_COLOR_ID']]['CONS_DZN_GMTS'];
				$prev_req_qnty=$prv_req_data[$row['PO_ID']][$row['TRIM_GROUP']][strtoupper(trim($row['ITEM_DESCRIPTION']))][$row['GMTS_COLOR_ID']];
				$total_req_qnty=$prev_req_qnty+$row['REQSN_QTY'];
				$balance_qnty=$required_qnty-$total_req_qnty;
				if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $item_group_arr[$row['TRIM_GROUP']]; ?></td>
					<td><? echo $row['ITEM_DESCRIPTION']; ?></td>
					<td align="right"><? echo $cons_dzn_gmts; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row['CONS_UOM']]; ?></td>
					<td align="right"><? echo number_format($required_qnty,4);?></td>
					<td align="right"><? echo number_format($row['CONS'],4);?></td>
					<td align="right"><? echo number_format($prev_req_qnty,4);?></td>
					<td align="right"><? echo number_format($row['REQSN_QTY'],4);?></td>
                    <td align="right"><? echo number_format($total_req_qnty,4);?></td>
                    <td align="right"><? echo number_format($balance_qnty,4);?></td>
					<td><? echo $row['REMARKS']; ?></td>
				</tr>
				<?
				$i++;
			}
	        ?>
	        </tbody>
	    </table>
        
        <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" width="400" style="margin-top:30px;">
	        <thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="200">Lot/Batch Number</th>
					<th width="100">Qty</th>
					<th>UOM</th>
				</tr>
	        </thead>
	        <tbody>
			<?
			// var_dump($all_data_info);
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($batch_wise_qnty_arr as $batch_no=>$batch_qnty)
			{
				if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $batch_no; ?></td>
					<td align="right"><? echo $batch_qnty["qnty"]; ?></td>
					<td align="center">Pcs</td>
				</tr>
				<?
				$i++;
				$batch_tot_qnty+=$batch_qnty["qnty"];
			}
	        ?>
	        </tbody>
            <tfoot>
            	<tr bgcolor="#CCCCCC" style="font-weight:bold">
					<td align="right">Total:</td>
					<td align="right"><? echo $batch_tot_qnty; ?></td>
					<td align="center">Pcs</td>
				</tr>
            </tfoot>
	    </table>
		</div>
		 <?
            echo signature_table(320, $data[0], $table_width."px");
         ?>
	</div>
	<?
	exit();
}



?>
