<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_com_department")
{
	echo create_drop_down( "cbo_department_name", 150, "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/get_pass_entry_controller', this.value, 'load_drop_down_section', 'section_td' );",0 );
	exit();
}

if ($action=="load_drop_down_location")
{
  echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", 0, "set_multiselect('cbo_location_id','0','0','','0')" );
  exit();
}

if ($action=="gatePass_search")
{
  echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	$selected_index = $company;
	?>
     
	<script>
		function js_set_value(str)
		{			
			$("#hidden_sys_number").val(str);
			parent.emailwindow.hide(); 
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="770" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<thead>
					<th width="130" class="must_entry_caption">Company</th>
					<th width="140">System ID</th>
					<th width="250" class="must_entry_caption">Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
				</thead>
				<tbody>
					<tr>
						<td><?php 
							echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $selected_index,"");
						?></td>
						<td> <input name="txt_get_pass" id="txt_get_pass"  style="width:140px" class="text_boxes" /></td>
	
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
						</td> 
						<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_get_pass').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_name').value, 'create_sys_search_list_view', 'search_div', 'returnable_item_receive_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
						</td>
					</tr>
					<tr>                  
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
						</td>
					</tr>    
				</tbody>
			</table>    
			<div valign="top" id="search_div"></div> 
			</form>
		</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_sys_search_list_view")
{	
	$ex_data = explode("_",$data);
	$getpass_id = str_replace("'","",$ex_data[0]);
	$fromDate = str_replace("'","",$ex_data[1]);
	$toDate = str_replace("'","",$ex_data[2]);
	$company_id= str_replace("'","",$ex_data[3]);

	if($company_id==0 && $fromDate =="" && $toDate =="")
	{
		echo "Please Select Company Name OR Date Range field Value"; die;
	}
	
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$supplier_name_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	
 	if($company_id !=0) $company_cond="and company_id = '$company_id'"; else $company_cond="";
	if( $getpass_id!="" )  $get_cond=" and sys_number_prefix_num like '".$getpass_id."' "; else  $get_cond="";
	if( $getpass_id!="" )  $get_cond1=" and a.sys_number_prefix_num like '".$getpass_id."' "; else  $get_cond1="";
	
	if( $fromDate!=0 && $toDate!=0 ) $sql_cond= " and out_date  between '".change_date_format($fromDate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($toDate,'yyyy-mm-dd',"-",1)."'";
	
	//echo $outIds_cond;
	$sql_system_id_search = "select id, company_id, sys_number_prefix_num, sys_number, basis,within_group, sent_by, sent_to, out_date,challan_no, extract( year from insert_date) as year
	from inv_gate_pass_mst
	where status_active=1 and is_deleted=0 and returnable=1 $get_cond $sql_cond $company_cond
	order by sys_number desc";//$outIds_cond

	$sqlResult = sql_select($sql_system_id_search);
	?>
    <div style="width:930px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" style="margin-right: 17px;">
            <thead>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="130" align="center">Gate Pass No</th>
                <th width="50" align="center">Prefix</th>
                <th width="50" align="center">Year</th>
                <th width="100" align="center">Basis </th>
                <th width="80" align="center">Sent By</th>                    
                <th width="150" align="center">Sent To</th>
                <th width="80" align="center">Out Date</th>
                <th align="center">Challan No</th>
            </thead> 
        </table>
    </div>
    <div style="width:920px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900px" class="rpt_table" id="list_view">
        <?
        $i=1;
			foreach($sqlResult as $selectResult)
			{
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$basis=$selectResult[csf('basis')];
				$within_group=$selectResult[csf('within_group')];
				if($basis==1)
				{ 
					if($within_group==1)
					{
						$send_to_company=$company_library[$selectResult[csf('sent_to')]];
					}
					else
					{
						$send_to_company=$selectResult[csf('sent_to')];		
					}
				}
				if($basis==16 || $basis==22 || $basis==20 || $basis==25 ||$basis==15 || $basis==1 || $basis==18 || $basis==19 || $basis==50)
				{ 
					if($within_group==1)
					{
						$send_to_company=$company_library[$selectResult[csf('sent_to')]];
					}
					else
					{
						$send_to_company=$selectResult[csf('sent_to')];		
					}
				}

				else if($basis==8 || $basis==9)
				{
					$send_to_company=$selectResult[csf('sent_to')];	
				}
				else if($basis==12)
				{
					if($within_group==2) {
						$send_to_company=$selectResult[csf('sent_to')];
					} else {
						$send_to_company=$supplier_name_library[$selectResult[csf('sent_to')]];
					}
				}
				else
				{
						//echo $within_group.'=='.$basis;
						if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7 || $basis==10 || $basis==11 || $basis==13 ||  $basis==14 ||  $basis==21 ||  $basis==28 ||  $basis==38 || $basis==52)
						{
							if($within_group==2)
							{
								$send_to_company=$selectResult[csf('sent_to')];
							}
							else
							{
								$send_to_company=$company_library[$selectResult[csf('sent_to')]];
							}
						}
						else
						{
								$send_to_company=$selectResult[csf('sent_to')];	
						}
				}
				//
				?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('id')]."_".$selectResult[csf("sys_number")]; ?>','','');" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="120"><?php echo $company_library[$selectResult[csf('company_id')]];  ?></td>
                    <td width="130" align="center"><p><? echo $selectResult[csf('sys_number')]; ?></p></td>
                    <td width="50" align="center"><p><?php echo $selectResult[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $selectResult[csf('year')]; ?></p></td>
                    <td width="100" align="center"><p><?php echo $get_pass_basis[$selectResult[csf('basis')]]; ?></p></td>
                    <td width="80" align="center"><p><?php  echo $selectResult[csf('sent_by')]; ?></p></td>
                    <td width="150" align="center"><p><?php  echo $send_to_company; ?></p></td>
                    <td width="80" align="center"><p><?php echo change_date_format($selectResult[csf('out_date')]); //$serving_company; ?></p></td>
                    <td align="center"><p><? echo $selectResult[csf('challan_no')]; ?></p></td>
                    
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


//report generated here--------------------//
if($action=="generate_report")
{ 
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process ));
  
  $cbo_company_name=str_replace("'","",$cbo_company_name);
  $cbo_department_name=str_replace("'","",$cbo_department_name);
  $cbo_location_id=str_replace("'","",$cbo_location_id);
  $cbo_item_cat=str_replace("'","",$cbo_item_cat);
  $txt_gate_pass=str_replace("'","",$txt_gate_pass);
  $txt_gate_id=str_replace("'","",$txt_gate_id);
  $txt_date_from=str_replace("'","",$txt_date_from);
  $txt_date_to=str_replace("'","",$txt_date_to);
  $report_type=str_replace("'","",$type);

  $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
  $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
  $department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');

  if($cbo_location_id!='') $location_cond=" and a.com_location_id in ($cbo_location_id)"; else $location_cond="";
  if($cbo_item_cat!=0) $item_category_cond=" and c.item_category_id=$cbo_item_cat"; else $item_category_cond.="";
  if($cbo_company_name!=0) $company_conds.=" and a.company_id=$cbo_company_name"; else $company_conds.="";
  if($cbo_department_name!=0) $department_cond.=" and a.department_id=$cbo_department_name"; else $department_cond.="";

  if($txt_gate_pass=='')$gate_pass_cond="";else $gate_pass_cond=" and a.id =$txt_gate_id "; //a.inv_gate_pass_mst_id

  if($db_type==0)
  {
    if($txt_date_from!="" && $txt_date_to!="") $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $out_date_cond=""; 
  }
  else
  {
    if($txt_date_from!="" && $txt_date_to!="") $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $out_date_cond="";
  }
  ob_start();
  ?>
  <style>
    .wrd_brk{word-break: break-all;}
    .left{text-align: left;}
    .center{text-align: center;}
    .right{text-align: right;}
  </style>

  <fieldset style="width:1520px;">
    <div style="width:1520px;">
      <table width="1500"  cellpadding="0" cellspacing="0" border="0"  align="left">                        
        <tr>
          <td colspan="14" align="center" style="font-size:16px; font-weight:bold" >Returnable Item Receive Report</td>              
        </tr>
      </table>
      <br />
      <table width="1500" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left"> 
        <thead>
          <tr>
            <th width="30">SL</th>
            <th width="100">GATE PASS DATE</th>
            <th width="120">GATE PASS NO</th>
            <th width="120">COMPANY</th>
            <th width="120">LOCATION</th>
            <th width="100" >DEPARTMENT</th>
            <th width="100" >Send to</th>
            <th width="120">GATE IN NO</th>
            <th width="80">GATE IN DATE</th>
            <th width="150">ITEM NAME</th>
            <th width="100">CATEGORY</th>
            <th width="80">Gate Pass QTY</th>
            <th width="60">UOM</th>
            <th width="80">Return Qty</th>
            <th >Balance Qty</th>
          </tr>
        </thead>
      </table> 
      <div style="width:1520px; overflow-y: scroll; max-height:350px;" id="scroll_body" align="left">
        <table width="1500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
          <tbody>
            <?
            $sql_gate_in="SELECT a.id as ID,a.sys_number as SYS_NUMBER_PASS,a.out_date as OUT_DATE,a.company_id as COMPANY_ID,a.com_location_id as COM_LOCATION_ID,a.sent_to as SENT_TO,a.department_id as DEPARTMENT_ID,b.id as GATE_ID, b.sys_number as SYS_NUMBER_IN,b.in_date as IN_DATE,c.item_category_id as ITEM_CATEGORY_ID,c.item_description as ITEM_DESCRIPTION, c.quantity as QUANTITY,c.uom as UOM,c.chalan_qty as CHALAN_QTY
            FROM inv_gate_pass_mst a, inv_gate_in_mst b, inv_gate_in_dtl c
            WHERE a.id=b.inv_gate_pass_mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $department_cond $location_cond $item_category_cond $company_conds $gate_pass_cond $out_date_cond 
            order by a.id,c.item_category_id,c.item_description"; 
            // echo $sql_gate_in;die;
            $gate_in_data=sql_select($sql_gate_in);
            
            //echo "<pre>";
            //print_r($gate_in_data); die;
            //var_dump($gate_in_data);

            $rowspan_arr=array(); $gate_in_qty_arr=array();$gate_id_arr=array();
            $gate_pass_qty=0;
            foreach ($gate_in_data as $val) 
            {
              $rowspan_arr[$val['ID']][$val['ITEM_DESCRIPTION']]++;
              $gate_in_qty_arr[$val['ID']][$val['ITEM_DESCRIPTION']]+=$val['QUANTITY'];
              $gate_id_arr[$val['ID']][$val['ITEM_DESCRIPTION']].=$val['GATE_ID'].',';
            }
            //echo "<pre>";print_r($total_blance_arr);
            $i=1;$row_chk=array();$row_chk2=array();
            $tot_challan=$tot_quantity=$tot_balance=0;
            foreach($gate_in_data as $val)
            {
              if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
              $rowspan=$rowspan_arr[$val['ID']][$val['ITEM_DESCRIPTION']];
              $return_qnty=$gate_in_qty_arr[$val['ID']][$val['ITEM_DESCRIPTION']];
              $gate_id_all=$gate_id_arr[$val['ID']][$val['ITEM_DESCRIPTION']];
              ?>
                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('troutp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="troutp_<? echo $i; ?>">
                  <td width="30" class="wrd_brk center"><? echo $i; ?></td>
                  <td width="100" class="wrd_brk center">&nbsp;<? echo change_date_format($val["OUT_DATE"]);?></td>
                  <td width="120" class="wrd_brk"><? echo $val["SYS_NUMBER_PASS"]; ?></td>
                  <td width="120" class="wrd_brk"><? echo $company_arr[$val["COMPANY_ID"]]; ?></td>
                  <td width="120" class="wrd_brk"><? echo $location_arr[$val["COM_LOCATION_ID"]]; ?></td>
                  <td width="100" class="wrd_brk"><? echo $department_arr[$val["DEPARTMENT_ID"]] ?></td>
                  <td width="100" class="wrd_brk"><? echo $department_arr[$val["SENT_TO"]] ?></td>
                  <td width="120" class="wrd_brk center"><? echo $val["SYS_NUMBER_IN"]; ?></td>
                  <td width="80" class="wrd_brk center">&nbsp;<? echo change_date_format($val["IN_DATE"]);?></td>
                  <td width="150" class="wrd_brk"><? echo $val["ITEM_DESCRIPTION"]; ?></td>
                  <td width="100" class="wrd_brk"><? echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?></td>
                  <?
                  if(!in_array($val['ID'].'**'.$val['ITEM_DESCRIPTION'],$row_chk))
                  {
                    $row_chk[]=$val['ID'].'**'.$val['ITEM_DESCRIPTION'];
                    ?>
                      <td width="80" class="wrd_brk right" rowspan="<?=$rowspan;?>" valign="middle"><? echo number_format($val["CHALAN_QTY"],2);$tot_challan+=$val["CHALAN_QTY"]; ?></td>
                    <?
                  }
                  ?>
                  <td width="60" class="wrd_brk center"><? echo $unit_of_measurement[$val["UOM"]]; ?></td>
                  <?
                  if(!in_array($val['ID'].'**'.$val['ITEM_DESCRIPTION'],$row_chk2))
                  {
                    $row_chk2[]=$val['ID'].'**'.$val['ITEM_DESCRIPTION'];
                    ?>
                      <td width="80" class="wrd_brk right" rowspan="<?=$rowspan;?>" valign="middle">
                        <a href='#report_details' onClick="fnc_return_qnty('<? echo $val['COMPANY_ID']; ?>','<? echo $val['ID']; ?>','<? echo rtrim($gate_id_all,','); ?>','<? echo $val['ITEM_DESCRIPTION']; ?>');">
                          <? echo number_format($return_qnty,2);$tot_quantity+=$return_qnty; ?>
                        </a>
                      </td>
                      <td class="wrd_brk right" rowspan="<?=$rowspan;?>" valign="middle"><? echo number_format($val["CHALAN_QTY"]-$return_qnty,2);$tot_balance+=$val["CHALAN_QTY"]-$return_qnty; ?></td>
                    <?
                  }
                  ?>
                </tr>
              <?
              $i++;
            }
            ?>   
          </tbody>
          <tfoot>
            <th colspan="11" class="right"><b>TOTAL &nbsp;</b></th> 
            <th class="wrd_brk right"><? echo number_format($tot_challan,2);?></th> 
            <th></th> 
            <th class="wrd_brk right"><? echo number_format($tot_quantity,2);?></th> 
            <th class="wrd_brk right"><? echo number_format($tot_balance,2);?></th>  
          </tfoot>
        </table>
      </div>
    </div>
  </fieldset>
  <?
  foreach (glob("$user_id*.xls") as $filename) 
  {
    if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
  }
  //---------end------------//
  $name=time();
  $filename=$user_id."_".$name.".xls";
  $create_new_doc = fopen($filename, 'w');
  $is_created = fwrite($create_new_doc,ob_get_contents());
  $filename=$user_id."_".$name.".xls";
  echo "$total_data####$filename####$report_type";
  exit();
}

if($action=="return_qnty_data")
{
  echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
  extract($_REQUEST);
   //echo $system_id; die;
     $sql="SELECT a.id as ID,a.in_date as IN_DATE,b.id as DTLS_ID,b.item_category_id as ITEM_CATEGORY_ID,b.item_description as ITEM_DESCRIPTION,b.quantity as QUANTITY
            from inv_gate_in_mst a, inv_gate_in_dtl b
            where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($gate_in_id) and a.company_id='$cbo_company' and a.inv_gate_pass_mst_id='$gate_pass_id' and b.item_description='$item_description'";
      $data_arr=sql_select($sql);  
    // echo $sql; die;
    
  ?> 
    <style>
      .wrd_brk{word-break: break-all;}
      .left{text-align: left;}
      .center{text-align: center;}
      .right{text-align: right;}
    </style>   
    <div id="data_panel" align="center" style="width:100%">
      <fieldset style="width: 98%">
        <table width="600" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
          <thead>
            <tr>
              <th width="50">Sl</th>
              <th width="100">Date</th>
              <th width="100">Category</th>
              <th width="150">Item Description</th>
              <th width="100">Qty.</th>
            </tr>
          </thead>  
          <tbody>
            <?
              $i=1;
              foreach ($data_arr as $row) 
              {     
                ?>                         
                <tr>
                    <td width="50" class="wrd_brk center"><? echo $i; ?></td>
                    <td width="100" class="wrd_brk center"><? echo change_date_format($row['IN_DATE']); ?></td>
                    <td width="100" class="wrd_brk center"><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
                    <td width="150" class="wrd_brk"><? echo $row['ITEM_DESCRIPTION']; ?></td>
                    <td width="100" class="wrd_brk right"><? echo number_format($row['QUANTITY'],2); ?></td>
                </tr>
                <?
                $i++;                                     
              }
            ?>
          </tbody>       
        </table>
      </fieldset>
    </div> 
    <?
    exit(); 
}
?>