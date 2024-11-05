<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All --", 0, "load_drop_down( 'requires/woven_shrinkage_and_shade_controller',this.value, 'load_drop_down_brand', 'brand_td' );" );
	exit();
}

if($action=="load_drop_down_buyer_2")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All --", 0, "load_drop_down( 'woven_shrinkage_and_shade_controller',this.value, 'load_drop_down_brand', 'brand_td' );" );
	exit();
}

if ($action=="load_drop_down_location")
{
	$sql_data=sql_select("select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0");
	if(count($sql_data)==1){$selected=$sql_data[0][csf('id')];}

	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down("cbo_brand_id", 150, "select id,brand_name from LIB_BUYER_BRAND where is_deleted = 0 AND status_active = 1 and BUYER_ID=$data ORDER BY brand_name ASC","id,brand_name", 1, "-- All --", $selected, "");
	exit();
}

if ($action=="load_drop_down_season")
{
	list($buyer_id,$season,$select_year)=explode('_',$data);
	echo create_drop_down( "cbo_season_name", 87, "select id,SEASON_NAME from LIB_BUYER_SEASON where BUYER_ID='$buyer_id' and id=$season and status_active =1 and is_deleted=0 order by SEASON_NAME","id,SEASON_NAME", 1, "-- All --", $season, "" );
	echo create_drop_down( "cbo_season_year", 60, $year,"", 1, "-- All --", $select_year, "" );
	exit();
}

/*if ($action=="load_drop_down_color")
{
	echo create_drop_down( "cbo_gmts_color_id", 150, "select id,COLOR_NAME from lib_color where status_active =1 and is_deleted=0 and id in($data) order by COLOR_NAME","id,COLOR_NAME", 1, "-- All --", 0, "" );
	exit();
}*/

/*if ($action=="pattern_data_popup")
{
	echo load_html_head_contents("Pattern", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$otal_row=10;
	$dataArr=array();
	foreach(explode('**',$pattern_data) as $pattern_data_arr){
		list($pattern_up_id,$pattern_no,$lengthMax,$lengthMin,$widthMax,$widthMin)=explode('__',$pattern_data_arr);
		$dataArr[$pattern_no]=array(
			pattern_up_id=>$pattern_up_id,
			lengthMax=>$lengthMax,
			lengthMin=>$lengthMin,
			widthMax=>$widthMax,
			widthMin=>$widthMin
		);
		
	}
		
	?>
	<!-- <script>
	var tot_row=<?=$otal_row;?>;
		function fnc_close()
		{
			var patternDataArr=Array();
			for(var i=1; i<=tot_row; i++)
			{
				var pattern_no=i;
				var pattern_up_id=$('#pattern_up_id_'+i).val();
				var length_max=$('#length_max_'+i).val();
				var length_min=$('#length_min_'+i).val();
				var width_max=$('#width_max_'+i).val();
				var width_min=$('#width_min_'+i).val();
				var lengthWidth=pattern_up_id+'__'+pattern_no+'__'+length_max+'__'+length_min+'__'+width_max+'__'+width_min;
				patternDataArr.push(lengthWidth);
			}

			$('#txt_selected_data').val(patternDataArr.join('**'));
			parent.emailwindow.hide();
		}
	</script> 
	</head>-->
	<?  ?>
	<!-- <body>
		<div align="center" style="width:315px;">
			<form name="searchbatchnofrm"  id="searchbatchnofrm">
				<fieldset style="width:305px;">
					<input type="hidden" name="txt_selected_data" id="txt_selected_data"/>
					<table cellpadding="0" cellspacing="0" rules="1" border="1" width="300" class="rpt_table">
						<thead>
							<tr>
								<th colspan="5">Pattern Summary</th>
							</tr>
							<tr>
								<th rowspan="2">Pattern No</th>
								<th colspan="2">Length (Warp)%</th>
								<th colspan="2">Width (Weft)%</th>
							</tr>
							<tr>
								<th width="50">Max</th>
								<th width="50">Min</th>
								<th width="50">Max</th>
								<th width="50">Min</th>
							</tr>
						</thead>
						<tbody>
						<? //for($i=1;$i<=$otal_row;$i++){?>
							<tr>
								<td align="center">P<?=$i;?><input type="hidden" id="pattern_up_id_<?=$i;?>" value="<?=$dataArr[$i][pattern_up_id];?>"></td>
								<td><input type="text" id="length_max_<?=$i;?>" value="<?=$dataArr[$i][lengthMax];?>" class="text_boxes" style="width:45px;"></td>
								<td><input type="text" id="length_min_<?=$i;?>" value="<?=$dataArr[$i][lengthMin];?>" class="text_boxes" style="width:45px;"></td>
								<td><input type="text" id="width_max_<?=$i;?>" value="<?=$dataArr[$i][widthMax];?>" class="text_boxes" style="width:45px;"></td>
								<td><input type="text" id="width_min_<?=$i;?>" value="<?=$dataArr[$i][widthMin];?>" class="text_boxes" style="width:45px;"></td>
							</tr>
						<? //} ?>
						</tbody>
						<tfoot>
							<td colspan="5" align="center" height="30">
							<input type="button" value="Done" onClick="fnc_close()" style="width:100px" class="formbutton">
							</td>
						</tfoot>
					</table>
					<div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body> -->
	<!-- <script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>-->
	<?
/*}*/

if($action=="sys_data_popup")
{
	echo load_html_head_contents("Sys Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str)
		{
			$("#selected_data").val(str); 
			parent.emailwindow.hide();
		}	
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="1100" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th>Company</th>
							<th>Buyer</th>
							<th>Brand</th>
							<th>Job NO</th>
							<th>Style Ref.</th>
							<th>Sys Id</th>
							<th>Insert Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<? echo create_drop_down("cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $cbo_company_id, "load_drop_down( 'woven_shrinkage_and_shade_controller',this.value, 'load_drop_down_buyer_2', 'buyer_td' );"); ?>
							</td>
							<td  id="buyer_td"><? echo create_drop_down("cbo_buyer_id", 150, "","", 1, "-- All --", $selected, ""); ?></td>
							<td id="brand_td">
							<? echo create_drop_down("cbo_brand_id", 150, "", 1, "-- All --", $selected, ""); ?>
							</td>
							
							<td><input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:110px;"  /></td>
							<td><input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:110px;" /></td>
							<td><input type="text" id="txt_sys_id" name="txt_sys_id" class="text_boxes" style="width:110px;"/></td>
							
							
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							
							
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="if (form_validation('cbo_company_id','Company')==false){ return; };show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_sys_id').value, 'sys_search_list_view', 'search_div', 'woven_shrinkage_and_shade_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
							<input type="hidden" id="selected_data" value="">
							</td>
						</tr>
						<tr>
							<td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script>$( "#cbo_company_id" ).change();</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="sys_search_list_view")
{
	list($company_id,$buyer_id,$brand_id,$job_no,$style_ref,$date_from,$date_to,$sys_id) = explode("_",$data);

	$buyer_arr=return_library_array( "select id, BUYER_NAME from lib_buyer",'id','BUYER_NAME');

	$season_arr=return_library_array( "select id,SEASON_NAME from LIB_BUYER_SEASON where status_active =1 and is_deleted=0 order by SEASON_NAME",'id','SEASON_NAME');
	
	
	$gmt_color_arr=return_library_array( "select id,COLOR_NAME from lib_color where status_active =1 and is_deleted=0 order by COLOR_NAME",'id','COLOR_NAME');
	
	
	
	
	if($job_no!=''){$whereCon.=" and a.JOB_NO like('%$job_no')";}
	if($style_ref!=''){$whereCon.=" and a.STYLE_REF like('%$style_ref%')";}
	if($buyer_id!=0){$whereCon.=" and a.BUYER_ID=$buyer_id";}
	if($brand_id!=0){$whereCon.=" and a.BRAND_ID=$brand_id";}
	if($sys_id!=""){$whereCon.=" and a.SYS_NUMBER like('%$sys_id')";}
	if( $date_from!="" && $date_to!="" )
	{
		$whereCon .= " and a.INSERT_DATE  between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)." 11:59:59 PM'";
	}
 	
	
	
	$sql="SELECT a.ID,a.SYS_NUMBER,A.BUYER_ID,A.BRAND_ID,a.JOB_NO,A.STYLE_REF ,a.SEASON_YEAR,a.SEASON_NAME,a.GMTS_COLOR_ID,a.INSERT_DATE FROM WOVEN_SHRINK_SHADE_MST A WHERE a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and A.COMPANY_ID=$company_id $whereCon order by a.id desc";
	//echo $sql; 
	$sql_result=sql_select($sql);
	
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1000" align="left">
		<thead>
			<tr>
				<th width="35">SL</th>
				<th width="100">System Number</th>
				<th width="100">Buyer</th>
				<th width="80">Job No</th>
				<th width="150">Merch Style Ref.</th>
				<th width="80">Buyer Season</th>
				<th width="50">Season Year</th>
                <th width="130">Gmt. Color</th>
                <th>Insert Date</th>
			</tr>
		</thead>
	</table>

	<div style="width:1020px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden; float:left;" id="scroll_body">
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1000" id="list_view" align="left">
			<tbody>
				<?
				$i=1;
				
				foreach($sql_result as $row)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
 					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[ID]; ?>')">
						<td width="35" align="center"><?=$i; ?></td>
						<td width="100"><p><?=$row[SYS_NUMBER];?></p></td>
						<td width="100"><p><?=$buyer_arr[$row[BUYER_ID]];?></p></td>
						<td width="80" align="center"><p><?=$row[JOB_NO]; ?></p></td>
						<td width="150"><p><?=$row[STYLE_REF]; ?></p></td>
                        <td width="80"><p><?=$season_arr[$row[SEASON_NAME]]; ?></p></td>
                        <td width="50"><p><?=$row[SEASON_YEAR]; ?></p></td>
                        <td width="130"><p><?=$gmt_color_arr[$row[GMTS_COLOR_ID]]; ?></p></td>
                        <td><p><?=date('d-m-Y h:i:s a',strtotime($row[INSERT_DATE])); ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</div>
	<?
	exit();

}

if($action=="job_data_popup")
{
	echo load_html_head_contents("Job Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($barcodeNoExisting==""){$barcodeNoExisting=0;}
	?>
	<script>
		function js_set_value(str)
		{
			var existingBarcode=str.split("_");
			if(existingBarcode[2]>0)
			{
				alert("Data already exist");
				return;
			}
			$("#selected_data").val(str); 
			parent.emailwindow.hide();
		}
	</script>

	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th>Search By</th>
							<!-- <th align="center" id="search_by_td_up">Enter Job Number</th> -->
							<th align="center" id="search_by_td_up">GRN Number</th>
							<th>GRN Date</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								//$search_by = array(1=>'Job No',2=>'PO No',3=>'Style');
								$search_by = array(1=>'GRN Number');
								//$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								$dd="change_search_event(this.value, '0', '0', '../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $barcodeNoExisting; ?>, 'job_search_list_view', 'search_div', 'woven_shrinkage_and_shade_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<input type="hidden" id="selected_data" value="" />
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="job_search_list_view")
{
	list($search_by,$search_data,$from_date,$to_date,$company_id,$cbo_year,$barcodeNoExisting) = explode("_",$data);

	$buyer_arr=return_library_array( "select id, BUYER_NAME from lib_buyer",'id','BUYER_NAME');

	$season_arr=return_library_array( "select id,SEASON_NAME from LIB_BUYER_SEASON where status_active =1 and is_deleted=0 order by SEASON_NAME",'id','SEASON_NAME');
	$brand_arr=return_library_array( "select id, BRAND_NAME from LIB_BUYER_BRAND",'id','BRAND_NAME');
	
	
	
	//if($search_by==1 && $search_data!=''){$whereCon=" and a.JOB_NO like('%$search_data')";}
	if($search_by==1 && $search_data!=''){$whereCon=" and d.recv_number like('%$search_data')";}
	//else if($search_by==2 && $search_data!=''){$whereCon=" and b.PO_NUMBER like('%$search_data%')";}
	//else if($search_by==3 && $search_data!=''){$whereCon=" and a.STYLE_REF_NO like('%$search_data')";}
	
	if( $from_date!="" && $to_date!="" )
	{
		$whereCon .= " and d.RECEIVE_DATE  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
	}
	$year_id=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";
	}
	elseif($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(a.insert_date,'yyyy')=$year_id"; else $year_cond="";
	}
	
	//$sql="SELECT a.SEASON_YEAR,a.SEASON_BUYER_WISE,a.ID,a.JOB_NO,A.JOB_NO,A.STYLE_REF_NO,A.BUYER_NAME,a.BRAND_ID,b.PO_NUMBER  FROM WO_PO_DETAILS_MASTER A ,WO_PO_BREAK_DOWN b WHERE a.id=b.job_id and A.COMPANY_NAME=$company_id $whereCon";
	$sql="SELECT a.SEASON_YEAR,a.SEASON_BUYER_WISE,a.ID,A.JOB_NO,A.STYLE_REF_NO,A.BUYER_NAME,a.BRAND_ID,d.RECV_NUMBER,d.BOOKING_NO 
	FROM WO_PO_DETAILS_MASTER A ,WO_PO_BREAK_DOWN b,WO_BOOKING_DTLS c,COM_PI_ITEM_DETAILS e,INV_RECEIVE_MASTER d
	WHERE a.id=b.job_id and b.job_no_mst=c.job_no and b.id=c.po_break_down_id and c.booking_no=e.WORK_ORDER_NO and d.booking_id=e.pi_id  and d.entry_form=559 and d.item_category=3 and d.receive_basis=1 and A.COMPANY_NAME=$company_id $whereCon $year_cond and d.recv_number not in(select f.grn_no from WOVEN_SHRINK_SHADE_MST f where d.recv_number=f.grn_no  and f.entry_form=670 and f.is_deleted=0 and f.status_active=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0  group by a.SEASON_YEAR,a.SEASON_BUYER_WISE,a.ID,A.JOB_NO,A.STYLE_REF_NO,A.BUYER_NAME,a.BRAND_ID,d.RECV_NUMBER,d.BOOKING_NO 
	union all 
	SELECT a.SEASON_YEAR,a.SEASON_BUYER_WISE,a.ID,A.JOB_NO,A.STYLE_REF_NO,A.BUYER_NAME,a.BRAND_ID,d.RECV_NUMBER,d.BOOKING_NO  FROM WO_PO_DETAILS_MASTER A ,WO_PO_BREAK_DOWN b,WO_BOOKING_DTLS c,INV_RECEIVE_MASTER d WHERE a.id=b.job_id and b.job_no_mst=c.job_no and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.entry_form=559 and d.item_category=3 and d.receive_basis=2 and A.COMPANY_NAME=$company_id $whereCon $year_cond and d.recv_number not in(select f.grn_no from WOVEN_SHRINK_SHADE_MST f where d.recv_number=f.grn_no and f.entry_form=670 and f.is_deleted=0 and f.status_active=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   group by a.SEASON_YEAR,a.SEASON_BUYER_WISE,a.ID,A.JOB_NO,A.STYLE_REF_NO,A.BUYER_NAME,a.BRAND_ID,d.RECV_NUMBER,d.BOOKING_NO";
	
	$sql_result=sql_select($sql);
	
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="700" align="left">
		<thead>
			<tr>
				<th width="35">SL</th>
				<th width="100">Buyer</th>
				<th width="100">Brand</th>
				<th width="80">Job No</th>
				<th width="150">Style Ref</th>
				<th width="80">Buyer Season</th>
				<th>Season Year</th>
				
			</tr>
		</thead>
	</table>

	<div style="width:720px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden; float:left;" id="scroll_body">
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="700" id="list_view" align="left">
			<tbody>
				<?
				$i=1;
				
				foreach($sql_result as $row)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
 					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[ID]."_".$row[RECV_NUMBER]."_".$barcodeNoExisting; ?>')">
						<td width="35" align="center"><?=$i; ?></td>
						<td width="100"><p><?=$buyer_arr[$row[BUYER_NAME]];?></p></td>
						<td width="100"><p><?=$brand_arr[$row[BRAND_ID]];?></p></td>
						<td width="80" align="center"><p><?=$row[JOB_NO]; ?></p></td>
						<td width="150"><p><?=$row[STYLE_REF_NO]; ?></p></td>
                        <td width="80"><p><?=$season_arr[$row[SEASON_BUYER_WISE]]; ?></p></td>
                        <td><p><?=$row[SEASON_YEAR]; ?></p></td>
				
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</div>
	<?
	exit();

}

if($action=="populate_job_data")
{

	$data=explode("_",$data);
	$barcodeNoExisting=$data[2];

	$sql="SELECT a.COMPANY_NAME,a.JOB_NO, A.STYLE_REF_NO,A.BUYER_NAME,a.BRAND_ID,a.SEASON_YEAR,a.SEASON_BUYER_WISE,b.PO_NUMBER,c.COLOR_NUMBER_ID  FROM WO_PO_DETAILS_MASTER A ,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c WHERE a.id=b.job_id and a.id=c.job_id and c.PO_BREAK_DOWN_ID=b.id and A.id=$data[0]";
	$sql_result_res = sql_select($sql);
	$colorIdArr=array();
	foreach($sql_result_res as $rows){
		if($rows[COLOR_NUMBER_ID]>0){$colorIdArr[$rows[COLOR_NUMBER_ID]]=$rows[COLOR_NUMBER_ID];}
	}
	$colorIdStr=implode(',',$colorIdArr);
	
	$row=$sql_result_res[0];
	
	//echo "load_drop_down( 'requires/woven_shrinkage_and_shade_controller','$colorIdStr', 'load_drop_down_color', 'td_gmtd_color' );\n";
	
	echo "load_drop_down( 'requires/woven_shrinkage_and_shade_controller',$row[BUYER_NAME]+'_'+$row[SEASON_BUYER_WISE]+'_'+$row[SEASON_YEAR], 'load_drop_down_season', 'td_season_season_year' );\n";

	
	echo "$('#txt_grn_no').val('".$data[1]."');\n";
	echo "$('#txt_job_no').val('".$row[JOB_NO]."');\n";
	//echo "$('#txt_style_ref').val('".$row[STYLE_REF_NO]."');\n";
	echo "$('#cbo_buyer_id').val('".$row[BUYER_NAME]."');\n";
	echo "$('#cbo_brand_id').val('".$row[BRAND_ID]."');\n";
	//echo "$('#cbo_season_name').val('".$row[SEASON_BUYER_WISE]."');\n";
	//echo "$('#cbo_season_year').val('".$row[SEASON_YEAR]."');\n";
 	exit();
}

/*if($action=="excel_file_upload")
{
	
	$filename = $_FILES['file']['name'][0];
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	$path = 'dtls_excel_file.'.$ext;
	if(move_uploaded_file($_FILES['file']['tmp_name'][0],$path)){

		include( '../../../ext_resource/excel/excel_reader.php' );
		$excel = new Spreadsheet_Excel_Reader($path); 
		$numRows=$excel->sheets[0]['numRows'];
		//$numCols=$excel->sheets[0]['numCols'];
		
		$dataArr=array();
		for($i=2;$i<=$numRows;$i++){
				
				$dataArr[data][]= array(
				CCL_NO=>$excel->sheets[0]['cells'][$i][1],
				INTELLOCUT_ROLL_NO=>$excel->sheets[0]['cells'][$i][2],	
				ROLL_NO=>$excel->sheets[0]['cells'][$i][3],
				LENGTH_YDS=>$excel->sheets[0]['cells'][$i][4],
				WIDTH=>$excel->sheets[0]['cells'][$i][5],	
				SHADE=>$excel->sheets[0]['cells'][$i][6],	
				BEFORE_WASH_LENGTH=>$excel->sheets[0]['cells'][$i][7],
				BEFORE_WASH_WIDTH=>$excel->sheets[0]['cells'][$i][8],	
				AFTER_WASH_LENGTH=>$excel->sheets[0]['cells'][$i][9],
				AFTER_WASH_WIDTH=>$excel->sheets[0]['cells'][$i][10],
				BEFORE_WASH_GSM=>$excel->sheets[0]['cells'][$i][11],
				AFTER_WASH_GSM=>$excel->sheets[0]['cells'][$i][12]
				);
		
		}
		
		echo json_encode($dataArr);
	}

 	exit();
}*/

if($action=="populate_sys_data")
{

	//mst..........................................
	$sql="SELECT a.ID, a.SYS_NUMBER_PREFIX, a.SYS_NUMBER_PREFIX_NUM, a.SYS_NUMBER, a.COMPANY_ID, a.LOCATION_ID, a.JOB_NO, a.BUYER_ID, a.BRAND_ID, a.SEASON_NAME, a.SEASON_YEAR, a.STYLE_REF, a.GMTS_COLOR_ID, a.CONSIGMENT, a.TOLARENCE_PER, a.REMARKS,a.GRN_NO FROM WOVEN_SHRINK_SHADE_MST A WHERE a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and A.id=$data";
	 //echo $sql;die;
	$sql_result_res = sql_select($sql);
	$row=$sql_result_res[0];
	
	
	//dtls.....................................
	$sql_dtls="SELECT b.ID, b.MST_ID, b.CCL_NO,b.INTELLOCUT_ROLL_NO,b.BARCODE_NO,b.GRN_DTLS_ID, b.ROLL_NO, b.LENGTH_YDS, b.WIDTH, b.SHADE, b.BEFORE_WASH_LENGTH_CM, b.BEFOREW_WASH_WIDTH_CM, b.AFTER_WASH_LENGTH_CM, b.AFTER_WASH_WIDTH_CM, b.BEFORE_WASH_GSM,b.AFTER_WASH_GSM  FROM WOVEN_SHRINK_SHADE_DTLS b WHERE b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.MST_ID=".$row[ID]." order by id";
	//  echo $sql_dtls;die;
	$sql_dtls_result_res = sql_select($sql_dtls);
	$i=0;
	foreach($sql_dtls_result_res as $drows){

		$ii=$i+1;
		if($i!=0){echo "addBreakDownTr($i);\n";}
		$after_wash_legth=number_format($drows[AFTER_WASH_LENGTH_CM], 2,'.','');
		
		echo "$('#dtlsID_".$i."').val('".$drows[ID]."');\n";	
		echo "$('#grnDtlsId_".$i."').val('".$drows[GRN_DTLS_ID]."');\n";	
		//echo "$('#cclNo_".$i."').val('".$drows[CCL_NO]."');\n";		
		echo "$('#cclNo_".$i."').val('".$ii."');\n";		
		echo "$('#intellocutRollNo_".$i."').val('".$drows[INTELLOCUT_ROLL_NO]."');\n";		
		echo "$('#barcodeNo_".$i."').val('".$drows[BARCODE_NO]."');\n";		
		//echo "$('#rollNo_".$i."').val('".$drows[ROLL_NO]."');\n";		
		echo "$('#lengthYDS_".$i."').val('".$drows[LENGTH_YDS]."');\n";		
		echo "$('#width_".$i."').val('".$drows[WIDTH]."');\n";	

		echo "$('#shade_".$i."').val('".$drows[SHADE]."');\n";		
		echo "$('#beforeWashLengthCM_".$i."').val('".$drows[BEFORE_WASH_LENGTH_CM]."');\n";		
		echo "$('#beforeWashWidthCM_".$i."').val('".$drows[BEFOREW_WASH_WIDTH_CM]."');\n";		
		echo "$('#afterWashLengthCM_".$i."').val('".$after_wash_legth."');\n";		
		echo "$('#afterWashWidthCM_".$i."').val('".number_format($drows[AFTER_WASH_WIDTH_CM], 2,'.','')."');\n";
		$shrinkageWashLength=(($drows[AFTER_WASH_LENGTH_CM] - $drows[BEFORE_WASH_LENGTH_CM])/$drows[BEFORE_WASH_LENGTH_CM])*100;
		$shrinkagerWashWidth=(($drows[AFTER_WASH_WIDTH_CM] - $drows[BEFOREW_WASH_WIDTH_CM])/$drows[BEFOREW_WASH_WIDTH_CM])*100;
		echo "$('#shrinkageWashLengthCM_".$i."').val('".number_format($shrinkageWashLength, 2,'.','')."');\n";	
		echo "$('#shrinkagerWashWidthCM_".$i."').val('".number_format($shrinkagerWashWidth, 2,'.','')."');\n";	
		echo "$('#beforeWashGSM_".$i."').val('".$drows[BEFORE_WASH_GSM]."');\n";		
		echo "$('#afterWashGSM_".$i."').val('".$drows[AFTER_WASH_GSM]."');\n";
		$GSMVariance=(($drows[AFTER_WASH_GSM] - $drows[BEFORE_WASH_GSM])/$drows[BEFORE_WASH_GSM])*100;
		echo "$('#GSMVariance_".$i."').val('".number_format($GSMVariance, 2,'.','')."');\n";	
		$i++;
	}
	
	
	
	//pattern....................................
	$sql_pattern="SELECT c.ID, c.MST_ID, c.COLOR_ID, c.PATTERN_NO, c.LENGTH_MAX, c.LENGTH_MIN, c.WIDTH_MAX, c.WIDTH_MIN FROM WOVEN_SHRINK_SHADE_PATTERN_BR c WHERE c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.MST_ID=".$row[ID]."";
	 //echo $sql;die;
	$sql_pattern_result_res = sql_select($sql_pattern);
	$patterDataArr=array();
	foreach($sql_pattern_result_res as $prows){
		$patterDataArr[$prows[ID]]=$prows[ID].'__'.$prows[PATTERN_NO].'__'.$prows[LENGTH_MAX].'__'.$prows[LENGTH_MIN].'__'.$prows[WIDTH_MAX].'__'.$prows[WIDTH_MIN];
	}
	$patterDataStr=implode('**',$patterDataArr);
	
	
	
	
	
	//echo "load_drop_down( 'requires/woven_shrinkage_and_shade_controller','".$row[GMTS_COLOR_ID]."', 'load_drop_down_color', 'td_gmtd_color' );\n";
	
	echo "load_drop_down( 'requires/woven_shrinkage_and_shade_controller',$row[BUYER_ID]+'_'+$row[SEASON_NAME]+'_'+$row[SEASON_YEAR], 'load_drop_down_season', 'td_season_season_year' );\n";
	
	echo "$('#txt_sys_no').val('".$row[SYS_NUMBER]."');\n";
	echo "$('#txt_mst_id').val('".$row[ID]."');\n";
	echo "$('#cbo_company_id').val('".$row[COMPANY_ID]."');\n";
	echo "$('#cbo_location_id').val('".$row[LOCATION_ID]."');\n";
	echo "$('#txt_job_no').val('".$row[JOB_NO]."');\n";
	echo "$('#txt_grn_no').val('".$row[GRN_NO]."');\n";
	echo "$('#cbo_buyer_id').val('".$row[BUYER_ID]."');\n";
	echo "$('#cbo_brand_id').val('".$row[BRAND_ID]."');\n";
	echo "$('#cbo_season_name').val('".$row[SEASON_NAME]."');\n";
	echo "$('#cbo_season_year').val('".$row[SEASON_YEAR]."');\n";
	//echo "$('#txt_style_ref').val('".$row[STYLE_REF]."');\n";
	//echo "$('#cbo_gmts_color_id').val('".$row[GMTS_COLOR_ID]."');\n";
	echo "$('#txt_consigment').val('".$row[CONSIGMENT]."');\n";
	//echo "$('#txt_tolarence_per').val('".$row[TOLARENCE_PER]."');\n";
	echo "$('#txt_remarks').val('".$row[REMARKS]."');\n";
	//echo "$('#txt_pattern_data_str').val('".$patterDataStr."');\n";

	exit();
}

if($action=="populate_dtls_data")
{

	//mst..........................................
	$sql="SELECT a.ID, a.SYS_NUMBER_PREFIX, a.SYS_NUMBER_PREFIX_NUM, a.SYS_NUMBER, a.COMPANY_ID, a.LOCATION_ID, a.JOB_NO, a.BUYER_ID, a.BRAND_ID, a.SEASON_NAME, a.SEASON_YEAR, a.STYLE_REF, a.GMTS_COLOR_ID, a.CONSIGMENT, a.TOLARENCE_PER, a.REMARKS FROM WOVEN_SHRINK_SHADE_MST A WHERE a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and A.id=$data";
	 //echo $sql;die;
	$sql_result_res = sql_select($sql);
	$row=$sql_result_res[0];
	
	
	//dtls.....................................
	//$sql_dtls="SELECT b.ID, b.MST_ID, b.CCL_NO,b.INTELLOCUT_ROLL_NO, b.ROLL_NO, b.LENGTH_YDS, b.WIDTH, b.SHADE, b.BEFORE_WASH_LENGTH_CM, b.BEFOREW_WASH_WIDTH_CM, b.AFTER_WASH_LENGTH_CM, b.AFTER_WASH_WIDTH_CM, b.BEFORE_WASH_GSM,b.AFTER_WASH_GSM  FROM WOVEN_SHRINK_SHADE_DTLS b WHERE b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.MST_ID=".$row[ID]." order by id";


	$sql_dtls="select a.ID AS GRN_MST_ID , b.ID AS GRN_DTLS_ID,c.PO_BREAKDOWN_ID AS BOOKING_ID,c.BOOKING_NO ,c.BARCODE_NO,c.ROLL_NO,c.MANUAL_ROLL_NO,b.GSM,b.CUTABLE_WIDTH 
	from INV_RECEIVE_MASTER a,QUARANTINE_PARKING_DTLS b,PRO_ROLL_DETAILS c
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form=559 and c.entry_form=559 and b.booking_no=c.booking_no
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.recv_number = '$data'
	group by  a.ID , b.ID ,c.BARCODE_NO,c.ROLL_NO,c.MANUAL_ROLL_NO,b.GSM,b.CUTABLE_WIDTH,c.PO_BREAKDOWN_ID,c.BOOKING_NO   order by c.barcode_no asc";




	//  echo $sql_dtls;die;
	$sql_dtls_result_res = sql_select($sql_dtls);
	$i=0;
	foreach($sql_dtls_result_res as $drows){
		if($i!=0){echo "addBreakDownTr($i);\n";}
		//$after_wash_legth=number_format($drows[AFTER_WASH_LENGTH_CM], 2,'.','');
		
		$ii=$i+1;	
		echo "$('#grnDtlsId_".$i."').val('".$drows[GRN_DTLS_ID]."');\n";	
		//echo "$('#cclNo_".$i."').val('".$drows[CCL_NO]."');\n";		
		echo "$('#cclNo_".$i."').val('".$ii."');\n";		
		echo "$('#intellocutRollNo_".$i."').val('".$drows[MANUAL_ROLL_NO]."');\n";		
		echo "$('#barcodeNo_".$i."').val('".$drows[BARCODE_NO]."');\n";	
		echo "$('#beforeWashGSM_".$i."').val('".$drows[GSM]."');\n";
		echo "$('#width_".$i."').val('".$drows[CUTABLE_WIDTH]."');\n";
		

		echo "$('#bookingId_".$i."').val('".$drows[BOOKING_ID]."');\n";
		echo "$('#bookingNo_".$i."').val('".$drows[BOOKING_NO]."');\n";

		//echo "load_drop_down( 'requires/woven_shrinkage_and_shade_controller','', '', '' );\n";
	

		/*echo "$('#lengthYDS_".$i."').val('".$drows[LENGTH_YDS]."');\n";		
		echo "$('#width_".$i."').val('".$drows[WIDTH]."');\n";		
		echo "$('#shade_".$i."').val('".$drows[SHADE]."');\n";		
		echo "$('#beforeWashLengthCM_".$i."').val('".$drows[BEFORE_WASH_LENGTH_CM]."');\n";		
		echo "$('#beforeWashWidthCM_".$i."').val('".$drows[BEFOREW_WASH_WIDTH_CM]."');\n";		
		echo "$('#afterWashLengthCM_".$i."').val('".$after_wash_legth."');\n";		
		echo "$('#afterWashWidthCM_".$i."').val('".number_format($drows[AFTER_WASH_WIDTH_CM], 2,'.','')."');\n";
		$shrinkageWashLength=(($drows[AFTER_WASH_LENGTH_CM] - $drows[BEFORE_WASH_LENGTH_CM])/$drows[BEFORE_WASH_LENGTH_CM])*100;
		$shrinkagerWashWidth=(($drows[AFTER_WASH_WIDTH_CM] - $drows[BEFOREW_WASH_WIDTH_CM])/$drows[BEFOREW_WASH_WIDTH_CM])*100;
		echo "$('#shrinkageWashLengthCM_".$i."').val('".number_format($shrinkageWashLength, 2,'.','')."');\n";	
		echo "$('#shrinkagerWashWidthCM_".$i."').val('".number_format($shrinkagerWashWidth, 2,'.','')."');\n";	
		echo "$('#beforeWashGSM_".$i."').val('".$drows[BEFORE_WASH_GSM]."');\n";		
		echo "$('#afterWashGSM_".$i."').val('".$drows[AFTER_WASH_GSM]."');\n";
		$GSMVariance=(($drows[AFTER_WASH_GSM] - $drows[BEFORE_WASH_GSM])/$drows[BEFORE_WASH_GSM])*100;
		echo "$('#GSMVariance_".$i."').val('".number_format($GSMVariance, 2,'.','')."');\n";*/	
		$i++;
	}
	
	
	
	//pattern....................................
	//$sql_pattern="SELECT c.ID, c.MST_ID, c.COLOR_ID, c.PATTERN_NO, c.LENGTH_MAX, c.LENGTH_MIN, c.WIDTH_MAX, c.WIDTH_MIN FROM WOVEN_SHRINK_SHADE_PATTERN_BR c WHERE c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.MST_ID=".$row[ID]."";
	 //echo $sql;die;
	/*$sql_pattern_result_res = sql_select($sql_pattern);
	$patterDataArr=array();
	foreach($sql_pattern_result_res as $prows){
		$patterDataArr[$prows[ID]]=$prows[ID].'__'.$prows[PATTERN_NO].'__'.$prows[LENGTH_MAX].'__'.$prows[LENGTH_MIN].'__'.$prows[WIDTH_MAX].'__'.$prows[WIDTH_MIN];
	}
	$patterDataStr=implode('**',$patterDataArr);
	
	
	
	
	
	
	echo "load_drop_down( 'requires/woven_shrinkage_and_shade_controller',$row[BUYER_ID]+'_'+$row[SEASON_NAME]+'_'+$row[SEASON_YEAR], 'load_drop_down_season', 'td_season_season_year' );\n";
	
	echo "$('#txt_sys_no').val('".$row[SYS_NUMBER]."');\n";
	echo "$('#txt_mst_id').val('".$row[ID]."');\n";
	echo "$('#cbo_company_id').val('".$row[COMPANY_ID]."');\n";
	echo "$('#cbo_location_id').val('".$row[LOCATION_ID]."');\n";
	echo "$('#txt_job_no').val('".$row[JOB_NO]."');\n";
	echo "$('#cbo_buyer_id').val('".$row[BUYER_ID]."');\n";
	echo "$('#cbo_brand_id').val('".$row[BRAND_ID]."');\n";
	echo "$('#cbo_season_name').val('".$row[SEASON_NAME]."');\n";
	echo "$('#cbo_season_year').val('".$row[SEASON_YEAR]."');\n";
	echo "$('#txt_consigment').val('".$row[CONSIGMENT]."');\n";
	echo "$('#txt_remarks').val('".$row[REMARKS]."');\n";*/

	exit();
}


//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		  //mst data..........................................................................
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("WOVEN_SHRINK_SHADE_MST_PK_SEQ", "woven_shrink_shade_mst", $con);
			$new_system_id = explode("*", return_next_id_by_sequence("WOVEN_SHRINKAGE_AND_SHADE_MST_PK_SEQ", "woven_shrinkage_and_shade_mst",$con,1,str_replace("'","",$cbo_company_id),'WSS',1,date("Y",time())));

			$field_array="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id,entry_form, location_id,grn_no, job_no, buyer_id, brand_id, season_name, season_year, consigment, remarks,inserted_by, insert_date";
			$data_array="(".$id.",'".$new_system_id[1]."','".$new_system_id[2]."','".$new_system_id[0]."',".$cbo_company_id.",670,".$cbo_location_id.",".$txt_grn_no.",".$txt_job_no.",".$cbo_buyer_id.",".$cbo_brand_id.",".$cbo_season_name.",".$cbo_season_year.",".$txt_consigment.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		
		//pattern data..........................................................................
		
		//$pattern_field_array="id, mst_id,pattern_no,  length_max, length_min, width_max, width_min,inserted_by, insert_date";

		
		/*$txt_pattern_data_str=str_replace("'",'',$txt_pattern_data_str);
		foreach(explode('**',$txt_pattern_data_str) as $pattern_data_arr){
			list($pattern_up_id,$pattern_no,$lengthMax,$lengthMin,$widthMax,$widthMin)=explode('__',$pattern_data_arr);
				
			$pattern_id=return_next_id_by_sequence("WOVEN_SHRINK_SHADE_PTRN_PK_SEQ", "woven_shrink_shade_pattern_br", $con);
			
			if($pattern_data_array=="") $add_comma=""; else $add_comma=", ";
				$pattern_data_array.=$add_comma."(".$pattern_id.",".$id.",'".$pattern_no."','".$lengthMax."','".$lengthMin."','".$widthMax."','".$widthMin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$pattern_id++;
		}*/
		
		 
		 
		 //dtls data..........................................................................
		$dtls_field_array="id, mst_id, ccl_no,intellocut_roll_no,barcode_no,grn_dtls_id, length_yds, width, shade, before_wash_length_cm, beforeW_wash_width_cm, after_wash_length_cm, after_wash_width_cm, before_wash_gsm, after_wash_gsm,booking_id,booking_no, inserted_by, insert_date";

		
		for($i=0;$i<$total_rows;$i++){
				
			$dtls_id=return_next_id_by_sequence("WOVEN_SHRINK_SHADE_DTLS_PK_SEQ", "woven_shrink_shade_dtls", $con);
			
			$dtls_up_id="dtlsID_$i";
			$ccl_no="cclNo_$i";
			$intellocut_roll_no="intellocutRollNo_$i";
			//$roll_no="rollNo_$i";+
			$barcode_no="barcodeNo_$i";+
			$grn_dtls_id="grnDtlsId_$i";+
			$length_yds="lengthYDS_$i";
			$width="width_$i";
			$shade="shade_$i";
			$before_wash_length_cm="beforeWashLengthCM_$i";
			$beforeW_wash_width_cm="beforeWashWidthCM_$i";
			$after_wash_length_cm="afterWashLengthCM_$i";
			$after_wash_width_cm="afterWashWidthCM_$i";
			$before_wash_gsm="beforeWashGSM_$i";
			$after_wash_gsm="afterWashGSM_$i";
			$bookingId="bookingId_$i";
			$bookingNo="bookingNo_$i";

			
			if($dtls_data_array=="") $add_comma=""; else $add_comma=", ";
			$dtls_data_array.=$add_comma."(".$dtls_id.",".$id.",".$$ccl_no.",".$$intellocut_roll_no.",".$$barcode_no.",".$$grn_dtls_id.",".$$length_yds.",".$$width.",".$$shade.",".$$before_wash_length_cm.",".$$beforeW_wash_width_cm.",".$$after_wash_length_cm.",".$$after_wash_width_cm.",".$$before_wash_gsm.",".$$after_wash_gsm.",".$$bookingId.",'".$$bookingNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$dtls_id++;
		
		}
		
		
		
		$rID=sql_insert("woven_shrink_shade_mst",$field_array,$data_array,0);
		//$rID2=sql_insert("woven_shrink_shade_pattern_br",$pattern_field_array,$pattern_data_array,0);
		$rID3=sql_insert("woven_shrink_shade_dtls",$dtls_field_array,$dtls_data_array,0);

		// echo "insert into woven_shrink_shade_dtls  $dtls_field_array value $pattern_data_array";die;
		//  echo "10**".$rID.'&& '.$rID2.'&& '.$rID3;oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID3==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_system_id[0];
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1  && $rID3==1)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_system_id[0];
			}
			else
			{
				oci_rollback($con);
				echo "10**0";
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$field_array_update="location_id*job_no*grn_no*buyer_id*brand_id*season_name*season_year*consigment*remarks*updated_by*update_date";
		$data_array_update=$cbo_location_id."*".$txt_job_no."*".$txt_grn_no."*".$cbo_buyer_id."*".$cbo_brand_id."*".$cbo_season_name."*".$cbo_season_year."*".$txt_consigment."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//pattern data..........................................................................
		
		//$pattern_field_array_update="pattern_no*length_max*length_min*width_max*width_min*updated_by*update_date";

		/*$txt_pattern_data_str=str_replace("'",'',$txt_pattern_data_str);
		foreach(explode('**',$txt_pattern_data_str) as $pattern_data_arr){
			list($pattern_up_id,$pattern_no,$lengthMax,$lengthMin,$widthMax,$widthMin)=explode('__',$pattern_data_arr);
			
			if($pattern_up_id!=''){
				$pattern_up_id_arr[]=$pattern_up_id;	
				$pattern_data_array_update_arr[$pattern_up_id] = explode("*", ("".$pattern_no."*'".$lengthMax."'*'".$lengthMin."'*'".$widthMax."'*'".$widthMin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else{
				$pattern_id=return_next_id_by_sequence("WOVEN_SHRINK_SHADE_PTRN_PK_SEQ", "woven_shrink_shade_pattern_br", $con);
				
				if($pattern_data_array=="") $add_comma=""; else $add_comma=", ";
					$pattern_data_array.=$add_comma."(".$pattern_id.",".$txt_mst_id.",'".$pattern_no."','".$lengthMax."','".$lengthMin."','".$widthMax."','".$widthMin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
		*/
		
		 //dtls data..........................................................................
		$dtls_field_array_update="ccl_no*intellocut_roll_no*length_yds*width*shade*before_wash_length_cm* beforeW_wash_width_cm*after_wash_length_cm*after_wash_width_cm*before_wash_gsm*after_wash_gsm*barcode_no*grn_dtls_id*updated_by*update_date";
		
		$dtlsIdArr=array();
		for($i=0;$i<$total_rows;$i++){
				
			$dtls_up_id="dtlsID_$i";
			$ccl_no="cclNo_$i";
			$intellocut_roll_no="intellocutRollNo_$i";
			$barcode_no="barcodeNo_$i";
			$grn_dtls_id="grnDtlsId_$i";
			//$roll_no="rollNo_$i";+
			$length_yds="lengthYDS_$i";
			$width="width_$i";
			$shade="shade_$i";
			$before_wash_length_cm="beforeWashLengthCM_$i";
			$beforeW_wash_width_cm="beforeWashWidthCM_$i";
			$after_wash_length_cm="afterWashLengthCM_$i";
			$after_wash_width_cm="afterWashWidthCM_$i";
			$before_wash_gsm="beforeWashGSM_$i";
			$after_wash_gsm="afterWashGSM_$i";

			if(str_replace("'",'',$$dtls_up_id)!=''){
				$dtlsIdArr[]=str_replace("'",'',$$dtls_up_id);
				$dtls_up_id_array[]=str_replace("'",'',$$dtls_up_id);
				$dtls_data_array_update_arr[str_replace("'",'',$$dtls_up_id)] = explode("*", ("".$$ccl_no."*".$$intellocut_roll_no."*".$$length_yds."*".$$width."*".$$shade."*".$$before_wash_length_cm."*".$$beforeW_wash_width_cm."*".$$after_wash_length_cm."*".$$after_wash_width_cm."*".$$before_wash_gsm."*".$$after_wash_gsm."*".$$barcode_no."*".$$grn_dtls_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else{
				$dtls_id=return_next_id_by_sequence("WOVEN_SHRINK_SHADE_DTLS_PK_SEQ", "woven_shrink_shade_dtls", $con);
				if($dtls_data_array=="") $add_comma=""; else $add_comma=", ";
				$dtls_data_array.=$add_comma."(".$dtls_id.",".$txt_mst_id.",".$$ccl_no.",".$$intellocut_roll_no.",".$$length_yds.",".$$width.",".$$shade.",".$$before_wash_length_cm.",".$$beforeW_wash_width_cm.",".$$after_wash_length_cm.",".$$after_wash_width_cm.",".$$before_wash_gsm.",".$$after_wash_gsm.",".$$barcode_no.",".$$grn_dtls_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
				$dtlsIdArr[]=$dtls_id;
			}
		}

		$flag=1;
		$txt_mst_id=str_replace("'","",$txt_mst_id);
		$rID=sql_update("woven_shrink_shade_mst",$field_array_update,$data_array_update,"id",$txt_mst_id,1);
		$flag=($flag==1 && $rID==1)?1:0;
		
		
		//$rID2=execute_query(bulk_update_sql_statement("woven_shrink_shade_pattern_br", "id", $pattern_field_array_update, $pattern_data_array_update_arr, $pattern_up_id_arr));
		
		//$pattern_field_array="id, mst_id,pattern_no,  length_max, length_min, width_max, width_min,inserted_by, insert_date";
		//$rID2_2=sql_insert("woven_shrink_shade_pattern_br",$pattern_field_array,$pattern_data_array,0);
		//$flag=($flag==1 && ($rID2==1 || $rID2_2==1))?1:0;
		
		
		
		$rID3 = execute_query(bulk_update_sql_statement("woven_shrink_shade_dtls", "id", $dtls_field_array_update, $dtls_data_array_update_arr, $dtls_up_id_array));
		
		$dtls_field_array="id, mst_id, ccl_no,intellocut_roll_no, length_yds, width, shade, before_wash_length_cm, beforeW_wash_width_cm, after_wash_length_cm, after_wash_width_cm,before_wash_gsm, after_wash_gsm,barcode_no,grn_dtls_id,inserted_by, insert_date";
		$rID4=sql_insert("woven_shrink_shade_dtls",$dtls_field_array,$dtls_data_array,0);
		$flag=($flag==1 &&($rID3==1 || $rID4==1))?1:0;
		
		
		$rID5 = execute_query("UPDATE woven_shrink_shade_dtls SET updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='$pc_date_time',STATUS_ACTIVE=0,IS_DELETED=1 WHERE id not in(".implode(',',$dtlsIdArr).") and mst_id=$txt_mst_id" );
		$flag=($flag==1 && $rID5==1)?1:0;
		
		// echo bulk_update_sql_statement("woven_shrink_shade_dtls", "id", $dtls_field_array_update, $dtls_data_array_update_arr, $dtls_up_id_array);die;
		
		 //echo "10**";print_r($pattern_data_array);die;oci_rollback($con);disconnect($con);die;
		 
		// echo "10**".$rID.'&& '.$rID2_2.'&& '.$rID3;oci_rollback($con);disconnect($con);die;
		

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$txt_mst_id."**".str_replace("'","",$txt_sys_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$txt_mst_id."**".str_replace("'","",$txt_sys_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		disconnect($con);
		die;
	}
}
 
/*if($action=="mrr_popup_search")
{
	
}*/

if($action=="print_report")
{ 
	extract($_REQUEST);
    $dataArr=explode('**',$data);
	// print_r($dataArr);
	// $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );	
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$brand_arr=return_library_array( "select id, brand_name from LIB_BUYER_BRAND", "id", "brand_name"  );

	$sql_company=sql_select("SELECT id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$dataArr[0]");
	
	$com_name=$sql_company[0][csf("company_name")];
	$com_address='';
	if($sql_company[0][csf("plot_no")] !=''){ $com_address.=$sql_company[0][csf("plot_no")];}
	if($sql_company[0][csf("level_no")] !=''){ $com_address.=", ".$sql_company[0][csf("level_no")];}
	if($sql_company[0][csf("road_no")] !=''){ $com_address.=", ".$sql_company[0][csf("road_no")];}
	if($sql_company[0][csf("block_no")] !=''){ $com_address.=", ".$sql_company[0][csf("block_no")];}
	if($sql_company[0][csf("city")] !=''){ $com_address.=", ".$sql_company[0][csf("city")];}
	if($sql_company[0][csf("zip_code")] !=''){ $com_address.=", ".$sql_company[0][csf("zip_code")];}

	$composition_arr=array(); $constructtion_arr=array();
	

	$pattern_sql="SELECT mst_id,color_id,pattern_no,length_max,length_min,width_max,width_min from woven_shrink_shade_pattern_br where mst_id=$dataArr[1] and STATUS_ACTIVE=1 and IS_DELETED=0 and length_max IS NOT NULL";
	// echo $pattern_sql;
	$pattern_data=sql_select($pattern_sql);
	foreach($pattern_data as $val){
		
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['length_max']=$val[csf("length_max")];
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['length_min']=$val[csf("length_min")];
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['width_max']=$val[csf("width_max")];
		$pattern_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]]['width_min']=$val[csf("width_min")];

		$pattern_no[$val[csf("mst_id")]][$val[csf("color_id")]][$val[csf("length_max")]][$val[csf("length_min")]][$val[csf("width_max")]][$val[csf("width_min")]]['pattern']=$val[csf("pattern_no")];

		$pattern_arr[$val[csf("pattern_no")]]="P".$val[csf("pattern_no")];

		$patternDataArr[$val[csf("mst_id")]][$val[csf("pattern_no")]]=array(
			length_max=>$val[csf("length_max")],
			length_min=>$val[csf("length_min")],
			width_max=>$val[csf("width_max")],
			width_min=>$val[csf("width_min")]

		);
	}

	// echo "<pre>";
	// print_r($patternDataArr);
	$sql="SELECT a.sys_number as SYS_NUMBER, a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.company_id as COMPANY_ID, a.job_no as JOB_NO, a.buyer_id as BUYER_ID, a.brand_id as BRAND_ID, a.season_name as SEASON_NAME, a.season_year as SEASON_YEAR, a.style_ref as STYLE_REF, a.gmts_color_id as GMTS_COLOR_ID, a.consigment as CONSIGMENT, a.tolarence_per as TOLARENCE_PER, b.ID as ID, b.MST_ID as MST_ID, b.ccl_no as CCL_NO,b.intellocut_roll_no as INTELLOCUT_ROLL_NO, b.roll_no as ROLL_NO, b.length_yds as ROLL_LENGHT_YDS, b.WIDTH as WIDTH, b.SHADE as SHADE, b.before_wash_length_cm as BEFORE_WASH_LENGTH_CM, b.beforew_wash_width_cm as BEFOREW_WASH_WIDTH_CM, b.after_wash_length_cm as AFTER_WASH_LENGTH_CM, b.after_wash_width_cm as AFTER_WASH_WIDTH_CM,a.insert_date as INSERT_DATE,a.remarks as REMARKS, b.before_wash_gsm as BEFORE_WASH_GSM, b.after_wash_gsm as AFTER_WASH_GSM 
	FROM woven_shrink_shade_mst a, woven_shrink_shade_dtls b WHERE a.id=$dataArr[1] and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.id=b.MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 order by b.id asc";

	// echo $sql;
	$data=sql_select($sql);
	$tolarence_val=$data[0]['TOLARENCE_PER'];
	$insert_date_arr=explode(" ",$data[0]['INSERT_DATE']);
	// echo "<pre>";
	//  print_r($data);
	// echo "<pre>";print_r($shade_arr);//die;
	//print_r($issue_rate_amnt_arr);
	?>
	<style>
		.wrd_brk{word-break: break-all;}
	</style>

	<fieldset style="width:1320px;" align="center">
		<table cellpadding="0" cellspacing="0" width="1310">
			<!-- <tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="12" style="font-size:18px"><strong><? echo " Shrinkage and Shade Report"; ?></strong></td>
			</tr> -->
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="12" style="font-size:18px"><strong><?echo "Company: ".$com_name; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="12" style="font-size:14px"><strong><?echo "Location: ".$com_address; ?></strong></td>
			</tr>
		</table>
		<br>
		<table cellpadding="0" cellspacing="0" border="0" width="1310">
			<tr>
				<td width="60"><b>Sys. ID.</b></td>
				<td width="30"><?=ltrim($data[0]['SYS_NUMBER_PREFIX_NUM'],0);?></td>
				<td width="60"><b>Job No</b></td>
				<td width="100"><?=$data[0]['JOB_NO'];?></td>
				<td width="50"><b>Buyer</b></td>
				<td width="80"><?=$buyer_arr[$data[0]['BUYER_ID']];?></td>
				<td width="50"><b>Brand</b></td>
				<td width="100"><?=$brand_arr[$data[0]['BRAND_ID']];?></td>
				<td width="100"><b>Merch Style Ref.</b></td>
				<td width="80"><?=$data[0]['STYLE_REF'];?></td>
				<td width="100"><b>Consigment</b></td>
				<td width="80">&nbsp;<?=$data[0]['CONSIGMENT'];?></td>
				<td width="90"><b>Gmt Color</b></td>
				<td width="80"><?=$color_arr[$data[0]['GMTS_COLOR_ID']];?></td>
				<td width="100"><b>Tolarence</b></td>
				<td width="30"><?=$tolarence_val;?></td>
				<td width="100"><b>Insert Date</b></td>
				<td ><?=change_date_format($insert_date_arr[0]);?></td>
			</tr>	
			<tr>
				<td ><b>Remarks</b></td>
				<td colspan="17"><?=$data[0]['REMARKS'];?></td>				
			</tr>			
		</table>
		<br>	
		<table width="1310" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
			<thead>
				<tr>
					<th width="30" rowspan="2">SL No</th>
					<th width="100" rowspan="2">Consigment Roll</th>
					<th width="100" rowspan="2">Manual Roll No</th>
					<th width="50" rowspan="2">Roll No</th>
					<th width="100" rowspan="2">Roll Length[Yds]</th>
					<th width="60" rowspan="2">Shade</th> 
					<th colspan="2" >Before Wash</th>
					<th colspan="2" >After Wash</th>
					<th colspan="2" >Shrinkage %</th>                        
					<th width="60" rowspan="2">Pattern No</th>
					<th width="100" colspan="2">Length (Warp)%</th>
					<th width="100" colspan="2">Width (Weft)%</th>
					<!-- <th width="80" colspan="3">Pattern Shrinkage</th> -->
					<th colspan="4">GSM</th>					
				</tr>
				<tr>
					<th width="70" >Length [CM]</th>
					<th width="70" >Width [CM]</th>
					<th width="70" >Length [CM]</th>
					<th width="70" >Width [CM]</th>
					<th width="50" >Length </th>
					<th width="50" >Width</th>                       
					<!-- <th width="80" >Width [CM]</th>
					<th width="80" >Length(Warp) </th> -->
					<th width="50" >Max</th>                      
					<th width="50" >Min</th>    
					<th width="50" >Max</th>                      
					<th width="50" >Min</th>                   
					<th width="50" >Before Wash</th>                      
					<th width="50" >After Wash</th>                      
					<th width="60" >Variance</th>                      
					<th >Var. %</th>                      
				</tr>
			</thead>
			<tbody>
			<?		
			$max_length=$min_length=$max_width=$min_width='';
			$i=1;
			foreach($data as $val){
				$shrinkage_length=(($val['AFTER_WASH_LENGTH_CM']-$val['BEFORE_WASH_LENGTH_CM'])/$val['BEFORE_WASH_LENGTH_CM'])*100;
				$shrinkage_width=(($val['AFTER_WASH_WIDTH_CM']-$val['BEFOREW_WASH_WIDTH_CM'])/$val['BEFORE_WASH_LENGTH_CM'])*100;

				// if($shrinkage_length >$max_length){$max_length=$shrinkage_length;}
				// if($max_length > $shrinkage_length){$min_length=$shrinkage_length;}
				// if($shrinkage_width >$max_width){$max_width=$shrinkage_width;}
				// if($max_width > $shrinkage_width){$min_width=$shrinkage_width;}
				if($max_length){
					if($shrinkage_length >$max_length){$max_length=$shrinkage_length;}
					if($min_length > $shrinkage_length){$min_length=$shrinkage_length;}
				}else{
					$max_length=$min_length=$shrinkage_length;
				}

				if($max_width){
					if($shrinkage_width >$max_width){$max_width=$shrinkage_width;}
					if($min_width > $shrinkage_width){$min_width=$shrinkage_width;}
				}else{
					$max_width=$min_width=$shrinkage_width;
				}
				$shrinkage_length=number_format($shrinkage_length, 2,'.','');
				$shrinkage_width=number_format($shrinkage_width, 2,'.','');
				$p_length_max=$pattern_data_arr[$val["MST_ID"]][$val["GMTS_COLOR_ID"]]['LENGTH_MAX'];
				$p_length_min=$pattern_data_arr[$val["MST_ID"]][$val["GMTS_COLOR_ID"]]['LENGTH_MIN'];
				$p_width_max=$pattern_data_arr[$val["MST_ID"]][$val["GMTS_COLOR_ID"]]['WIDTH_MAX'];
				$p_width_min=$pattern_data_arr[$val["MST_ID"]][$val["GMTS_COLOR_ID"]]['WIDTH_MIN'];
				
				$GSMVariance=(($val['AFTER_WASH_GSM']-$val['BEFORE_WASH_GSM'])/$val['BEFORE_WASH_GSM'])*100;
				$chkGSMVariance=ltrim($GSMVariance,'-');
				if($chkGSMVariance>$tolarence_val){$variance_color='red';}else{$variance_color='';}
				?>								
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
					<td width="30"><?=$i;?></td>
					<td width="100"><?=$val['CCL_NO'];?></td>
					<td width="100"><?=$val['INTELLOCUT_ROLL_NO'];?></td>
					<td width="50"><?=$val['ROLL_NO'];?></td>
					<td width="100"><?=$val['ROLL_LENGHT_YDS'];?></td>
					<td width="60" align="center"><?=$val['SHADE'];?></td> 
					<td width="70" align="right"><?=$val['BEFORE_WASH_LENGTH_CM'];?></td>
					<td width="70" align="right"><?=$val['BEFOREW_WASH_WIDTH_CM'];?></td>
					<td width="70" align="right"><?=$val['AFTER_WASH_LENGTH_CM'];?></td>                         
					<td width="70" align="right"><?=$val['AFTER_WASH_WIDTH_CM'];?></td>
					<td width="50" align="right"><?=number_format($shrinkage_length, 2,'.','');?></td>
					<td width="50" align="right"><?=number_format($shrinkage_width, 2,'.','');?></td>
						
					<? 
					$pattern_name=$warp_length_max=$warp_length_min=$warp_width_max=$warp_width_min='';
					foreach($patternDataArr[$val["MST_ID"]] as $pattern_no=>$drows){
						if(($shrinkage_length <= $drows[length_max]) && ($shrinkage_length >= $drows[length_min]) 	&& ($shrinkage_width <= $drows[width_max]) && ($shrinkage_width >= $drows[width_min]) ){
							// echo $pattern_arr[$pattern_no]."<br>";
							$pattern_name= $pattern_arr[$pattern_no];
							$warp_length_max=$drows[length_max];
							$warp_length_min=$drows[length_min];
							$warp_width_max=$drows[width_max];
							$warp_width_min=$drows[width_min];
							$patternNo[$pattern_arr[$pattern_no]]['length_max'] =$drows[length_max];
							$patternNo[$pattern_arr[$pattern_no]]['length_min'] =$drows[length_min];
							$patternNo[$pattern_arr[$pattern_no]]['width_max'] =$drows[width_max];
							$patternNo[$pattern_arr[$pattern_no]]['width_min'] =$drows[width_min];
							$patternNo[$pattern_arr[$pattern_no]]['patternNo'] +=1;
							$patternNo[$pattern_arr[$pattern_no]]['yds'] +=$val['ROLL_LENGHT_YDS'];
							break;
						}
					}
					?>
					<td width="60" align="center" bgcolor="<?php echo ($pattern_name=='' ? 'red' : '');?>" title="<?=$p_length_max.'_'.$p_length_min.'_'.$p_width_max.'_'.$p_width_min;?>">
						<? echo $pattern_name;?>
					</td>
					<td width="50" align="right"><?=$warp_length_max;?></td>
					<td width="50" align="right"><?=$warp_length_min;?></td>
					<td width="50" align="right"><?=$warp_width_max;?></td>
					<td width="50" align="right"><?=$warp_width_min;?></td>
					<td width="50" align="right"><?=$val['BEFORE_WASH_GSM'];?></td>
					<td width="50" align="right"><?=$val['AFTER_WASH_GSM'];?></td>
					<td width="60" align="right"><?=number_format($val['AFTER_WASH_GSM']-$val['BEFORE_WASH_GSM'],2);?></td>
					<td align="right" bgcolor="<?=$variance_color;?>"><?echo number_format($GSMVariance,2);?></td>
				</tr>
				<?
				$shade_arr[$val["SHADE"]]=$val["SHADE"] ;
				$shade_yds[$val["SHADE"]]['yds'] +=$val["ROLL_LENGHT_YDS"];	
				$shade_roll[$val["SHADE"]]['roll_no'] +=1;	
				$i++;	
			}
			?>
			</tbody>
		</table>
		<br>
		<table  width="1160" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
			<table width="380" cellpadding="0" cellspacing="0" style="float:left" border="1" rules="all" class="rpt_table" > 
				<thead>                                        
					<tr><th width="70" colspan="7" style="background:#ff9900" >Pattern Summary</th> </tr>
					<tr>
						<th width="70" rowspan="2" style="background:#ff9900">Pattern No</th>
						<th width="80" colspan="2" style="background:#ff9900">Length (Warp)</th>
						<th width="80" colspan="2" style="background:#ff9900">Width (Weft)</th>
						<th width="80" rowspan="2" style="background:#ff9900">No Of Rolls</th>
						<th rowspan="2" style="background:#ff9900">Yds</th>
					</tr>
					<tr>
						<th width="40" style="background:#ff9900">Max</th>
						<th width="40" style="background:#ff9900">Min</th>
						<th width="40" style="background:#ff9900">Max</th>
						<th width="40" style="background:#ff9900">Min</th>
					</tr>
				</thead>
					<?
					ksort($patternNo);
					foreach($patternNo as $pid => $val){
						?>
							<tr>
								<td width="70" align="center"><b><?=$pid;?></b></td>
								<td width="40" align="right"><?=$val['length_max'];?></td>
								<td width="40" align="right"><?=$val['length_min'];?></td>
								<td width="40" align="right"><?=$val['width_max'];?></td>
								<td width="40" align="right"><?=$val['width_min'];?></td>
								<td width="80" align="right"><?=$val['patternNo'];?></td>
								<td width="70" align="right"><?=$val['yds'];?></td>
							</tr>
						<?
					}
					?>
			</table> 
			<table width="200" cellpadding="0" cellspacing="0" style="float:left;margin-left:50px;" border="1" rules="all" class="rpt_table" > 
				<thead>                                        
					<th width="70" colspan="3" style="background:#3399ff">Shade Summary</th>		
				</thead>
				<thead>
					<th width="60" style="background:#3399ff">Shade </th>
					<th width="60" style="background:#3399ff">Rolls</th>
					<th width="80" style="background:#3399ff">Yds</th>
				</thead>
				<?
				// [$val[csf("shade")]]
				ksort($shade_arr);
				foreach($shade_arr as $shadeid => $shade_data){
					?>
						<tr>
							<td width="60" align="center"><b><?=$shade_data;?></b></td>
							<td width="60" ><?=$shade_roll[$shadeid]['roll_no'];?></td>
							<td width="80" ><?=$shade_yds[$shadeid]['yds'];?></td>
						</tr>
					<?
				}
				?>
				<tr>
			</table> 
			<table width="240" cellpadding="0" cellspacing="0" border="1" rules="all" style="float:left;margin-left:50px;" class="rpt_table">
				<thead>					
					<th width="80" colspan="4" style="background:#ff9933"  align="center">Max To Min</th>			
				</thead>						
				<tr>
					<td width="70" align="center"><b>Length</b></td>
					<td width="60"  align="center"><?=$max_length;?></td>
					<td width="40" align="center">To</td>
					<td width="60"  align="center"><?=$min_length;?></td>
				</tr>
				<tr>
					<td width="70" align="center"><b>Width</b></td>
					<td width="60"  align="center"><?=$max_width;?></td>
					<td width="40" align="center">To</td>
					<td width="60"  align="center"><?=$min_width;?></td>
				</tr>
			</table> 	
		<table>	  
	</fieldset>
	<?
    exit();
}

?>
