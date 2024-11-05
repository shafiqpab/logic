<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
$machine_name=return_library_array( "select machine_no,id from  lib_machine_name where is_deleted=0", "id", "machine_no"  );
if ($action=="load_drop_floor")
{
	$data=explode('_',$data);
	$loca=$data[0];
	$com=$data[1];
	//echo "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]'  order by floor_name";die;
	echo create_drop_down( "cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]' and production_process=4 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected,"load_drop_down( 'requires/drying_stentering_controller', document.getElementById('cbo_service_company').value+'**'+this.value, 'load_drop_machine', 'machine_td' );" );     	 
	exit();
}
if($action=="populate_restenter_from_data")
{
	if($db_type==2) $grop_con="listagg(cast(re_stenter_no as varchar2(4000)),',') within group (order by re_stenter_no) AS re_stenter_no";
	else
		 $grop_con="group_concat(re_stenter_no) AS re_stenter_no";
		 
	  $sql_data=("select batch_id, $grop_con  from pro_fab_subprocess where batch_id=$data and entry_form=31 and status_active=1 and is_deleted=0 group by batch_id");
	$data_arr=sql_select($sql_data);
	$re_stenter_no=(explode(",",$data_arr[0][csf("re_stenter_no")]));
	$stenter_no=end($re_stenter_no);
	//if($stenter_no>0 || $stenter_no==0) $restenter_no=$stenter_no+1; else $restenter_no=0;
	if($stenter_no>0 || count($data_arr)>0) $restenter_no=$stenter_no+1; else $restenter_no=0;
	echo "$('#txt_restenter_no').val('".$restenter_no."');\n";
	
}


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_machine")
{
$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
$data=explode('**',$data);
$com=$data[0];
$floor=$data[1];
if($db_type==2)
{
	echo create_drop_down( "cbo_machine_name", 135, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=4 and company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name","id,machine_name", 1, "-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value, 'populate_data_from_machine', 'requires/drying_stentering_controller' );","" );
}
else if($db_type==0)
{
echo create_drop_down( "cbo_machine_name", 135, "select id,concat(machine_no, '-' ,brand) as machine_name from lib_machine_name where category_id=4 and company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name","id,machine_name", 1, "-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value, 'populate_data_from_machine', 'requires/drying_stentering_controller' );","" );	
}
	exit();
}
if ($action=="populate_data_from_machine")
{ 
	$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
	$ex_data=explode('**',$data);
	 $sql_res="select id, floor_id, machine_group from lib_machine_name where id=$ex_data[2] and category_id=4 and company_id=$ex_data[0] and  floor_id=$ex_data[1] and status_active=1 and is_deleted=0 ";
	$nameArray=sql_select($sql_res);
	foreach ($nameArray as $row)
	{	
	echo "document.getElementById('txt_machine_no').value 			= '".$floor_arr[$row[csf("floor_id")]]."';\n";
	echo "document.getElementById('txt_mc_group').value 			= '".$row[csf("machine_group")]."';\n";
	}
	exit();
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	//print_r($data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_service_company", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", 0, "load_drop_down('requires/drying_stentering_controller', this.value, 'load_drop_floor', 'floor_td' )","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_service_company", 135, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_service_company", 135, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}

if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{ 
			$('#hidden_batch_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:800px;">
    <form name="searchbatchnofrm"  id="searchbatchnofrm">
        <fieldset style="width:790px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="770" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="4">
                          <?
							  echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                          ?>
                        </th>
                    </tr>                	
                    <tr>
                        <th width="150px">Batch Type</th>
                        <th width="150px">Batch No</th>
                        <th width="220px">Batch Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td align="center">	
                        <?
                            echo create_drop_down( "cbo_batch_type", 150, $order_source,"",0, "--Select--", 1,0,0 );
                        ?>
                    </td>
                     <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_batch" id="txt_search_batch" />	
                    </td> 
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
                    </td>
                   
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_batch_type').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_batch_search_list_view', 'search_div', 'drying_stentering_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="4">
                        <div style="width:100%; margin-left:3px;" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$start_date =$data[0];
	$end_date =$data[1];
	$company_id =$data[2];
	$batch_type =$data[3];
	$batch_no =$data[4];
	$search_type =$data[5];
 	
	if($search_type==1)
	{
		if ($batch_no!='') $batch_cond=" and batch_no='$batch_no'"; else $batch_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($batch_no!='') $batch_cond=" and batch_no like '%$batch_no%'"; else $batch_cond="";
	}
	else if($search_type==2)
	{
		if ($batch_no!='') $batch_cond=" and batch_no like '$batch_no%'"; else $batch_cond="";
	}
	else if($search_type==3)
	{
		if ($batch_no!='') $batch_cond=" and batch_no like '%$batch_no'"; else $batch_cond="";
	}	
	if($batch_type==0)
		$search_field_cond_batch="and entry_form in (0,36)";
	else if($batch_type==1)
		$search_field_cond_batch="and entry_form=0";
	else if($batch_type==2)
		$search_field_cond_batch="and entry_form=36";
	//echo $search_field_cond_batch;die;
	if($db_type==2)
		{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and batch_date between '".change_date_format($start_date, "mm-dd-yyyy", "-",1)."' and '".change_date_format($end_date, "mm-dd-yyyy", "-",1)."'"; else $batch_date_con ="";
		
		if($batch_type==0 || $batch_type==2)
			{
		
			$sql_po=sql_select("SELECT b.mst_id, a.job_no_mst,listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
			else
			{
			$sql_po=sql_select("SELECT b.mst_id, a.job_no_mst,listagg(cast(a.po_number as varchar2(4000)),',') within group (order by a.po_number) as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
		}
		if($db_type==0)
		{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and batch_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $batch_date_con ="";
		
		if($batch_type==0 || $batch_type==2)
			{
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat( distinct a.order_no )  as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
			else
			{
				
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat(distinct a.po_number)  as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
		}
	$po_num=array();
	
	foreach($sql_po as $row_po_no)
	{
	$po_num[$row_po_no[csf('mst_id')]]['po_no']=$row_po_no[csf('po_no')];
	$po_num[$row_po_no[csf('mst_id')]]['job_no_mst']=$row_po_no[csf('job_no_mst')];		
	} 	//and company_id=$company_id

	$sql_sales_job=array();
	$sql_sales_job= sql_select("SELECT b.job_no as job_no_mst,b.booking_no,f.job_no as sales_order_no,f.within_group from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, fabric_sales_order_mst f,pro_batch_create_mst g  where a.booking_no=b.booking_no and b.booking_no=f.sales_booking_no and b.po_break_down_id=c.id and g.booking_no=f.sales_booking_no $batch_date_con $batch_cond and g.status_active=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.job_no,b.booking_no,f.job_no,f.within_group");

	foreach ($sql_sales_job as $sales_job_row) {
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
	}


	$sql_sales_job2= sql_select("SELECT  a.sales_booking_no as  booking_no, a.job_no as sales_order_no  from   fabric_sales_order_mst a ,pro_batch_create_mst b    where a.sales_booking_no=b.booking_no and a.status_active=1 and a.within_group=2  $batch_date_con $batch_cond group by a.sales_booking_no , a.job_no ");

	foreach ($sql_sales_job2 as $sales_job_row) {
		 
		$sales_job_arr2[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		 
	}



	$sql = "SELECT id, batch_no, batch_date, batch_weight, booking_no, extention_no, color_id, batch_against, re_dyeing_from,is_sales from pro_batch_create_mst where batch_for in(0,1) and batch_against<>4 and status_active=1 and is_deleted=0 $search_field_cond_batch $batch_date_con $batch_cond order by id desc"; 
	//echo $sql;//die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Batch No</th>
                <th width="80">Extention No</th>
                <th width="80">Batch Date</th>
                <th width="90">Batch Qnty</th>
                <th width="115">Job No</th>
                <th width="80">Color</th>
                <th>Po/FSO No</th>
            </thead>
        </table>
        <div style="width:770px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				if($db_type==0) $select_group="group by a.id"; 
				else if($db_type==2) $select_group="group by a.id,a.po_number,a.job_no_mst";
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$is_sales = $selectResult[csf('is_sales')];
					$within_group=$sales_job_arr[$selectResult[csf('booking_no')]]["within_group"];
					$po_no = '';
					if ($selectResult[csf('re_dyeing_from')] == 0 || 1==1) {
						if($is_sales == 1){
							if($within_group == 1){
								$po_no = $sales_job_arr[$selectResult[csf('booking_no')]]["sales_order_no"];
								$job_no= $sales_job_arr[$selectResult[csf('booking_no')]]["job_no_mst"];
							}else{
								$po_no = $sales_job_arr2[$selectResult[csf('booking_no')]]["sales_order_no"];
								$job_no= "";}
						}else{
							$po_no = implode(",", array_unique(explode(",", $po_num[$selectResult[csf('id')]]['po_no'])));
							$job_no= $po_num[$selectResult[csf('id')]]['job_no_mst'];
						}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('id')].'_'.$is_sales; ?>')"> 
						<td width="40" align="center"><? echo $i; ?></td>	
						<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
						<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
						<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
						<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
						<td width="115"><p><? echo $job_no; ?></p></td>
						<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
						<td><? echo $po_no; ?></td>	
					</tr>
						<?
						$i++;
					}
					else
					{
						$sql_re= "select id, batch_no, batch_date, batch_weight, booking_no, MAX(extention_no) as extention_no, color_id, batch_against, re_dyeing_from from 
						pro_batch_create_mst where  batch_for in(0,1) and entry_form in(0,36) and batch_against<>4 and status_active=1 and is_deleted=0 and id=".
						$selectResult[csf('re_dyeing_from')]." group by id, batch_no, batch_date, batch_weight, booking_no,color_id, batch_against,re_dyeing_from ";
						//$dataArray=sql_select( $sql_re );
						$dataArray=array();
						foreach($dataArray as $row)
						{
							if($row[csf('re_dyeing_from')]==0)
							{
								$po_no=implode(",",array_unique(explode(",",$po_num[$selectResult[csf('id')]]['po_no'])));
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf
								('id')]; ?>)"> 
									<td width="40" align="center"><? echo $i; ?></td>	
									<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
									<td width="80"><p><? /*if($selectResult[csf('extention_no')]!=0)*/ echo $selectResult[csf('extention_no')]; ?></p></td>
									<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
									<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
                                    <td width="115"><p><? echo $po_num[$selectResult[csf('id')]]['job_no_mst']; ?></p></td>
									<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
									<td><? echo $po_no; ?></td>	
								</tr>
								<?
								$i++;
							}
						}
					}
				}
			?>
            </table>
        </div>
	</div>           
<?
exit();
}
///Issue Challan POPUP Start
if ($action=="issue_challan_popup")
{

	echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_company_id;die;
?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_system_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:860px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:860px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Issue Date Range</th>
                    
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Issue No</th>
                    <th>Service Source</th>
                     <th>Service Company</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    
                        
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Issue No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                     <td align="center">	
                    	<?
                       		 echo create_drop_down( "cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'drying_stentering_controller',this.value+'**'+$cbo_company_id,'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
						?>
                    </td> 
                     <td id="dyeing_company_td">
                    	<?
                                echo create_drop_down( "cbo_service_company", 135, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                        ?>
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_service_source').value+'_'+document.getElementById('cbo_service_company').value, 'create_challan_search_list_view', 'search_div', 'drying_stentering_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$cbo_service_source =$data[5];
	$cbo_service_company =$data[6];
//echo $cbo_service_source;
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and recv_number_prefix_num like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date, process_id, batch_id from inv_receive_mas_batchroll where entry_form=63 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Issue No</th>
            <th width="60">Year</th>
            <th width="120">Service Source</th>
            <th width="140">Service Company</th>
            <th width="110">Process</th>
            <th width="100">Batch</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				 
				$dye_comp="&nbsp;";
                if($row[csf('dyeing_source')]==1)
					$dye_comp=$company_arr[$row[csf('dyeing_company')]]; 
				else
					$dye_comp=$supllier_arr[$row[csf('dyeing_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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
if($action=="check_issue_challan_no")
{
	$data=explode("**",$data);
	//print_r($data);die;
	//$sql_result=sql_select("select  b.prod_id,b.width_dai_type,a.recv_number_prefix_num,b.roll_id,a.batch_id from pro_grey_batch_dtls  b, inv_receive_mas_batchroll a  where a.id=b.mst_id and b.mst_id=$data and a.entry_form=63 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");
	$sql="select  a.id,a.recv_number_prefix_num from  inv_receive_mas_batchroll a  where   a.recv_number_prefix_num=$data[1]  and a.company_id=$data[0] and a.entry_form=63 and a.status_active=1 and a.is_deleted=0 ";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('recv_number_prefix_num')];;
	}
	else
	{
		echo "0_";
	}
	exit();	
}
if($action=="check_issue_challan_no_scan")
{
	$data=explode("**",$data);
	//print_r($data);die;
	//$sql_result=sql_select("select  b.prod_id,b.width_dai_type,a.recv_number_prefix_num,b.roll_id,a.batch_id from pro_grey_batch_dtls  b, inv_receive_mas_batchroll a  where a.id=b.mst_id and b.mst_id=$data and a.entry_form=63 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");
	$sql="select  a.id,a.recv_number from  inv_receive_mas_batchroll a  where   a.recv_number='$data[1]'  and a.company_id=$data[0] and a.entry_form=63 and a.status_active=1 and a.is_deleted=0 ";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('recv_number_prefix_num')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}
if($action=="populate_data_from_data")
{
	
	//$ex_data=explode('_',$data);
	//$hidden_roll_id=$ex_data[0];
	//$batch_id=$ex_data[1];
	/*$update_barcode_arr=array();
	foreach($data_array_mst as $inf)
	{
		$update_barcode_arr[]="'".$inf[csf('barcode_no')]."'";
	}*/
	//echo $batch_id;die;
	$sql = "select id, company_id, recv_number_prefix_num, dyeing_source, dyeing_company, receive_date, batch_id, process_id from inv_receive_mas_batchroll where id=$data and entry_form=63 and status_active=1 and is_deleted=0 ";
	//echo $sql;
	if($db_type==2) $group_concat="listagg(roll_id ,',') within group (order by roll_id) as roll_id ";
	else if($db_type==0) $group_concat="group_concat(roll_id)  as roll_id ";
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_issue_chalan').val('".$row[csf("recv_number_prefix_num")]."');\n";
		//echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		//echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		//echo "$('#cbo_process').val(".$row[csf("process_id")].");\n";
		//echo "$('#txt_issue_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";
		echo "load_drop_down( 'requires/drying_stentering_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
		
		//$batchno = return_field_value("batch_no","pro_batch_create_mst","id='".$row[csf("batch_id")]."'");
		$roll_id_concat = return_field_value("$group_concat","pro_grey_batch_dtls","mst_id='".$data."' and roll_id>0 ","roll_id");
		$all_roll_concat=implode(",",array_unique(explode(",",$roll_id_concat))); 
		echo "$('#txt_roll_id').val('".$all_roll_concat."');\n";
		//echo "$('#txt_batch_no').val('".$batchno."');\n";	
		//echo "$('#hidden_batch_id').val(".$row[csf("batch_id")].");\n";
		echo "$('#txt_issue_mst_id').val(".$row[csf("id")].");\n";
		//echo $data_array_mst=sql_select("select a.entry_form,b.width_dia_type,a.company_id,b.item_description,b.width_dia_type,b.prod_id,b.roll_no,b.roll_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a where b.roll_id in($all_roll_concat)  and a.id=b.mst_id and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0   group by b.id,b.item_description,b.width_dia_type,b.prod_id,a.entry_form,b.roll_id,b.roll_no,a.company_id");
  	}
	exit();	
}
if($action=='issue_show_fabric_desc_listview')//Grey Issue Subcon
{
	//print($data);
	$ex_data=explode('_',$data);
	$hidden_roll_id=$ex_data[0];
	//$update_id=$ex_data[1];
	$batch_id=$ex_data[1];
	$fabric_roll_arr=array();
	/*$prollData=sql_select("select id,roll_no,barcode_no from pro_roll_details where  status_active=1 and is_deleted=0");
	foreach($prollData as $row)
	{
		$fabric_roll_arr[$row[csf('id')]]['roll']=$row[csf('roll_no')];
		//$fabric_roll_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		//$fabric_roll_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}*/
	$fabric_desc_arr=array();
	$prodData=sql_select("select id,detarmination_id, item_description, gsm, dia_width, product_name_details from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$fabric_desc_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
		$fabric_desc_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$fabric_desc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
	}
	//if($db_type==0) $select_group=" group by item_description"; 
	//else if($db_type==2) $select_group="group by b.id,b.item_description,b.width_dia_type,b.prod_id,a.entry_form,b.roll_no";//order by id desc limit 0,1
	$i=1;	
	
	$sql_result=sql_select("select a.entry_form,b.width_dia_type,a.company_id,b.item_description,b.width_dia_type,b.prod_id,b.roll_no,b.roll_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  b.width_dia_type in(1,2,3) and a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0  and b.roll_id in($hidden_roll_id) and b.mst_id=$batch_id and b.roll_id not in(select b.roll_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id='$batch_id' and a.entry_form=31 and b.status_active=1 and b.is_deleted=0 )  group by b.id,b.item_description,b.width_dia_type,b.prod_id,a.entry_form,b.roll_id,b.roll_no,a.company_id");
	if(count($sql_result)>0)
	{
	
		//$subprocess_dtls=return_field_value("id ", "pro_fab_subprocess_dtls", "id=$hidden_buyer_id  and status_active=1 and is_deleted=0","job_no");
		foreach($sql_result as $row)
		{
			$cons_comps='';
			$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
			//print_r($cons_comps_data);
			$z=0;
			foreach($cons_comps_data as $val)
			{
				if($z!=0)
				{
					$cons_comps.=$val." ";
				}
				$z++;
			}
			$compamy=$row[csf('company_id')];
			$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
			$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
			
			$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$compamy' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

			if($roll_maintained==1) 
			{
				$roll_no=return_field_value("roll_no","pro_roll_details","id ='".$row[csf('roll_id')]."' and is_deleted=0 and status_active=1");
			//$roll_no=$fabric_roll_arr[$row[csf('roll_id')]]['roll'];
			$readonly="readonly";
			$production_qty=$row[csf('batch_qnty')];
			$tot_production_qty+=$production_qty;
			}
			else
			{
			$roll_no=$row[csf('roll_no')];
			$readonly="";	
			$production_qty="";
			$tot_production_qty+=$production_qty;
			}
		?>
			<tr class="general" id="row_<? echo $i; ?>">
             	<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]" checked="checked"  > &nbsp; &nbsp;<? echo $i; ?></td>
				<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? echo $cons_comps; ?>" disabled/></td>
				<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
				<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? echo  $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td> 
				<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
                 <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
                </td>
                 <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $roll_no;?>"  style="width:35px;" <? echo $readonly; ?> /> <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />	</td>
				<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($row[csf('batch_qnty')],2); ?>" disabled/>
                	<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
				  	<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
				  	<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
				</td>
                <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($production_qty,2); ?>" /></td>
                <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>
                <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
			</tr>
		<?
			$b_qty+= $row[csf('batch_qnty')];
			$i++;
		}
	}
	
	else
	{ 
	
	
	 $sql=("select a.entry_form,b.width_dia_type,a.company_id,b.item_description,b.width_dia_type,b.prod_id,b.roll_no,b.roll_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and b.width_dia_type in(1,2,3) and a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0  and b.mst_id=$batch_id and b.roll_id not in(select b.roll_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id='$batch_id' and a.entry_form=31 and b.status_active=1 and b.is_deleted=0 )  group by b.id,b.item_description,b.width_dia_type,b.prod_id,a.entry_form,b.roll_id,b.roll_no,a.company_id");
	$sql_result=sql_select($sql);
	if(count($sql_result)>0)
	{
	foreach($sql_result as $row)
		{
			$cons_comps='';
			$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
			//print_r($cons_comps_data);
			$z=1;
			foreach($cons_comps_data as $val)
			{
				if($z!=0)
				{
					$cons_comps.=$val." ";
				}
				$z++;
			}
			$compamy=$row[csf('company_id')];
			$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
			$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
			
			$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$compamy' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

			if($roll_maintained==1) 
			{
				$roll_no=return_field_value("roll_no","pro_roll_details","id ='".$row[csf('roll_id')]."' and is_deleted=0 and status_active=1");
			//$roll_no=$fabric_roll_arr[$row[csf('roll_id')]]['roll'];
			$readonly="readonly";	
			$production_qty=$row[csf('batch_qnty')];
			$tot_production_qty+=$production_qty;
			}
			else
			{
			$roll_no=$row[csf('roll_no')];
			$readonly="";	
			$production_qty=$row[csf('batch_qnty')];
			$tot_production_qty+=$production_qty;
			}
		?>
			<tr class="general" id="row_<? echo $i; ?>">
             <td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked /> &nbsp; &nbsp;<? echo $i; ?></td>
				<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? echo $cons_comps; ?>" disabled/></td>
				<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
				<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? echo  $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td> 
				<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
                 <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
                </td>
                 <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $roll_no;?>" style="width:35px;" <? echo $readonly; ?> /> <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />	</td>
				<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($row[csf('batch_qnty')],2); ?>" disabled/>
                	<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
					<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
				  	<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
				</td>
                <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($production_qty,2); ?>" /></td>
                <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>
                <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
			</tr>
		<?
			$b_qty+= $row[csf('batch_qnty')];
			$i++;
		}
	}
	else
	{ ?>
		<tr class="general" id="row_<? echo $i; ?>">
        	<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked /> &nbsp; &nbsp;<? echo $i; ?></td>
			<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? //echo $cons_comps; ?>" disabled/></td>
			<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? //echo $gsm; ?>" /></td>
			<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? //echo  $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td> 
			<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? //echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
                 <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? //echo $row[csf('width_dia_type')];?>" readonly /></td>
            <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" value="<? //echo $roll_no;?>" style="width:35px;" readonly /> <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? //echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />	</td>
			<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? //echo number_format($row[csf('batch_qnty')],2); ?>" disabled/>
            	<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
				 <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? //echo $row[csf('prod_id')];?>" />
				 <input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly /></td>
            <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();" class="text_boxes_numeric" style="width:60px;"/></td>
           <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>
           <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
		</tr>
	<?
		}
	}?>
	<tr>
     	<td colspan="6" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
        <td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" value="<? echo number_format($b_qty,2); ?>" style="width:60px" readonly />  </b></td>
        <td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($production_qty,2); ?>" readonly /> </td>
        <td align="right"></td>           
        <td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" readonly /> </td>
     </tr>
	<?
	exit();
}
if($action=="roll_maintained_data")
{
	//$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=2 and variable_list=3 and is_deleted=0 and status_active=1");
	//if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	
	 $roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	
	echo "document.getElementById('roll_maintained').value 	= '".$roll_maintained."';\n";
	echo "document.getElementById('page_upto').value 		= '".$page_upto_id."';\n";

	
	exit();	
}
if($action=='populate_data_from_batch')
{ 
	$ex_data=explode('_',$data);	
	$batch_id=$ex_data[0]; 
	$is_sales=$ex_data[1]; 
	//$batch_no=$ex_data[1];
	//$roll_maintained=$ex_data[1];
	$company_id=return_field_value("company_id","pro_batch_create_mst","id ='$batch_id' and is_deleted=0 and status_active=1");
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	//echo $roll_maintained;die;
	$sql_sales_job=array();
	$sql_sales_job=sql_select("select a.buyer_id, b.job_no as job_no_mst,b.booking_no,f.job_no as sales_order_no,f.within_group from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, fabric_sales_order_mst f where a.booking_no=b.booking_no and b.booking_no=f.sales_booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.job_no,b.booking_no,f.job_no,f.within_group,a.buyer_id");

	foreach ($sql_sales_job as $sales_job_row) {
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["buyer_id"] = $sales_job_row[csf('buyer_id')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
	}
	$sql_sales_job2=sql_select("SELECT  a.sales_booking_no as  booking_no, a.job_no as sales_order_no ,a.buyer_id from   fabric_sales_order_mst a,pro_batch_create_mst b   where a.sales_booking_no=b.booking_no and b.id ='$batch_id' and a.status_active=1 and a.within_group=2 group by a.sales_booking_no , a.job_no,a.buyer_id ");

	foreach ($sql_sales_job2 as $sales_job_row) {
		 
		$sales_job_arr2[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr2[$sales_job_row[csf('booking_no')]]["buyer_id"] = $sales_job_row[csf('buyer_id')];
		 
	}



	if($db_type==0) $select_group_row="  order by a.id desc limit 1"; 
	else if($db_type==2) $select_group_row="and  rownum<=1 group by a.id,a.batch_no,a.batch_weight,a.color_id, 
	a.booking_without_order,a.batch_date,a.color_range_id,a.insert_date,a.company_id,a.process_id,a.entry_form,a.booking_no,a.total_trims_weight ";
	if($db_type==0) $pop_batch="order by a.id";
	else if($db_type==2) $pop_batch=" group by a.id,a.batch_no, a.batch_weight,batch_date,a.color_id,a.color_range_id,a.insert_date, a.booking_without_order,a.company_id,a.process_id,a.entry_form,a.booking_no,a.total_trims_weight  order by a.id";
	if($db_type==0) $select_list=" group_concat(distinct(b.po_id)) as po_id"; 
	else if($db_type==2) $select_list="listagg(b.po_id,',') within group (order by b.po_id) as po_id";
	
	if($batch_no!='')
	{ 
	$data_array=sql_select("SELECT a.id,a.batch_no,a.entry_form,a.batch_date,a.total_trims_weight, a.batch_weight,MAX(a.extention_no) as extention_no,a.company_id,a.process_id as process_id_batch,a.booking_no, 
	a.color_id,a.color_range_id,a.insert_date, a.booking_without_order, sum(b.batch_qnty) as batch_qnty, $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where  a.id=b.mst_id and a.id='$batch_id' and a.entry_form in(0,36)  $select_group_row");
	}
	else
	{
	$data_array=sql_select("SELECT a.id,a.batch_no,a.entry_form,a.total_trims_weight, a.batch_weight,Max(a.extention_no) as extention_no,a.company_id,a.batch_date,a.process_id as process_id_batch,a.booking_no, 
	a.color_id,a.color_range_id,a.insert_date, a.booking_without_order, sum(b.batch_qnty) as batch_qnty, $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where a.id='$batch_id' and a.entry_form in(0,36)  and a.id=b.mst_id $pop_batch");	
	}
	if($db_type==0) $select_f_group=""; 
	else if($db_type==2) $select_f_group="group by a.job_no_mst, b.buyer_name";
	if($db_type==0) $select_listagg="group_concat(distinct(a.po_number)) as po_no"; 
	else if($db_type==2) $select_listagg="listagg(cast(a.po_number as varchar2(4000)),',') within group (order by a.po_number) as po_no";
	if($db_type==0) $select_listagg_subcon="group_concat(distinct(a.order_no)) as po_no"; 
	else if($db_type==2) $select_listagg_subcon="listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no";
	
	foreach ($data_array as $row)
	{ 
		$pro_id=implode(",",array_unique(explode(",",$row[csf('po_id')])));
		//echo "load_drop_down( 'requires/drying_stentering_controller', '".$row[csf("company_id")]."', 'roll_maintained', 'roll_maintained' );\n";
		echo "document.getElementById('cbo_company_id').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('page_upto').value 		= '".$page_upto_id."';\n";
		if($roll_maintained==1)
		{
		echo "$('#txt_issue_chalan').attr('disabled',false);\n";	
		}
		echo "document.getElementById('roll_maintained').value 		= '".$roll_maintained."';\n";
	//	echo "load_drop_down( 'requires/drying_stentering_controller', '".$row[csf("company_id")]."', 'load_drop_floor', 'floor_td' );\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('hidden_batch_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_batch_ID').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_ext_id').value 					= '".$row[csf("extention_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_trims_weight').value 			= '".$row[csf("total_trims_weight")]."';\n";
		if($row[csf("entry_form")]==36)
		{
			$batch_type="<b> SUBCONTRACT ORDER BATCH</b>";
			$result_job=sql_select("select $select_listagg_subcon, b.subcon_job as job_no_mst, b.party_id as buyer_name from  subcon_ord_dtls a, 
			 subcon_ord_mst b where a.job_no_mst=b.subcon_job and a.id in(".$pro_id.") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 
			and a.is_deleted=0 group by b.subcon_job, b.party_id");
		}
		else
		{
			$batch_type="<b> SELF ORDER BATCH </b>";
			$result_job=sql_select("select $select_listagg, a.job_no_mst, b.buyer_name from wo_po_break_down a, 
			wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$pro_id.") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 
			and a.is_deleted=0 $select_f_group");
		}
		echo "document.getElementById('batch_type').innerHTML 			= '".$batch_type."';\n";
		$process_name_batch='';
		$process_id_array=explode(",",$row[csf("process_id_batch")]);
		foreach($process_id_array as $val)
		{
			//if($process_name_batch=="") $process_name_batch=$conversion_cost_head_array[$val]; else $process_name_batch.=",".$conversion_cost_head_array[$val];
		}
		//echo "document.getElementById('txt_process_id').value 			= '".$row[csf("process_id_batch")]."';\n";
		//echo "document.getElementById('txt_process_name').value 			= '".$process_name_batch."';\n";
		
		$pro_id2=implode(",",array_unique(explode(",",$result_job[0][csf("po_no")])));
		$within_group=$sales_job_arr[$row[csf('booking_no')]]["within_group"];
		if ($is_sales == 1) {
			if($within_group == 1){
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$sales_job_arr[$row[csf('booking_no')]]["buyer_id"]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '" . $sales_job_arr[$row[csf('booking_no')]]["job_no_mst"] . "';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $sales_job_arr[$row[csf('booking_no')]]["sales_order_no"] . "';\n";
			}else{
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$sales_job_arr2[$row[csf('booking_no')]]["buyer_id"]]  . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $sales_job_arr2[$row[csf('booking_no')]]["sales_order_no"] . "';\n";	
			}
		}else{
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$result_job[0][csf("buyer_name")]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '" . $result_job[0][csf("job_no_mst")] . "';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $pro_id2 . "';\n";
		}
		$sql_batch_d=sql_select("select id,batch_no,process_end_date,end_hours,end_minutes,machine_id,floor_id,process_id,remarks from pro_fab_subprocess where 		batch_id='$batch_id' and entry_form=30");
		foreach($sql_batch_d as $dyeing_d)
		{
			echo "document.getElementById('txt_slitting_date').value = '".change_date_format($dyeing_d[csf("process_end_date")])."';\n";
			echo "document.getElementById('txt_slitting_time').value = '".$dyeing_d[csf("end_hours")].':'.$dyeing_d[csf("end_minutes")]."';\n";
		}
		
		$sql_batch=sql_select("select id,batch_no,width_dia_type,process_end_date,process_start_date,start_hours,start_minutes,end_hours,end_minutes,machine_id,floor_id,process_id,temparature,stretch,over_feed,feed_in,pinning,speed_min,chemical_name,production_date,shift_name,remarks from pro_fab_subprocess where entry_form=31 and batch_id='$batch_id' ");
		foreach($sql_batch as $r_batch)
		{
			/*$process_name='';
			$process_id_array=explode(",",$r_batch[csf("process_id")]);
			foreach($process_id_array as $val)
			{
				if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}
			echo "document.getElementById('txt_update_id').value 			= '".$r_batch[csf("id")]."';\n";
			echo "document.getElementById('txt_process_end_date').value 	= '".change_date_format($r_batch[csf("process_end_date")])."';\n";
			echo "document.getElementById('txt_process_id').value 			= '".$r_batch[csf("process_id")]."';\n";
			echo "document.getElementById('cbo_dia_type').value 			= '".$r_batch[csf("width_dia_type")]."';\n";
			echo "document.getElementById('txt_process_name').value 		= '".$process_name."';\n";
			echo "document.getElementById('txt_temparature').value 			= '".$r_batch[csf("temparature")]."';\n";
			echo "document.getElementById('txt_stretch').value 				= '".$r_batch[csf("stretch")]."';\n";
			echo "document.getElementById('txt_feed').value 				= '".$r_batch[csf("over_feed")]."';\n";
			echo "document.getElementById('txt_feed_in').value 				= '".$r_batch[csf("feed_in")]."';\n";
			echo "document.getElementById('txt_pinning').value 				= '".$r_batch[csf("pinning")]."';\n";
			echo "document.getElementById('txt_speed').value 				= '".$r_batch[csf("speed_min")]."';\n";
			echo "document.getElementById('txt_chemical').value 			= '".$r_batch[csf("chemical_name")]."';\n";
			echo "document.getElementById('txt_process_date').value 		= '".change_date_format($r_batch[csf("production_date")])."';\n";
			echo "document.getElementById('txt_process_start_date').value 	= '".change_date_format($r_batch[csf("process_start_date")])."';\n";
			$start_hour=str_pad($r_batch[csf("start_hours")],2,'0',STR_PAD_LEFT);
			$start_minute=str_pad($r_batch[csf("start_minutes")],2,'0',STR_PAD_LEFT);
			echo "document.getElementById('txt_start_hours').value 				= '".$start_hour."';\n";
			echo "document.getElementById('txt_start_minutes').value 			= '".$start_minute."';\n";
			//echo "set_multiselect('cbo_sub_process','0','1','".$r_batch[csf('process_id')]."','0');\n";
			$minute=str_pad($r_batch[csf("end_minutes")],2,'0',STR_PAD_LEFT);
			$hour=str_pad($r_batch[csf("end_hours")],2,'0',STR_PAD_LEFT);
			echo "document.getElementById('txt_end_minutes').value	= '".$minute."';\n";
			echo "document.getElementById('txt_end_hours').value	= '".$hour."';\n";
			echo "load_drop_down( 'requires/drying_stentering_controller', document.getElementById('cbo_company_id').value+'**'+".$r_batch[csf("floor_id")].", 
			'load_drop_machine', 'machine_td' );\n";
			echo "document.getElementById('cbo_floor').value = '".$r_batch[csf("floor_id")]."';\n";
			echo "document.getElementById('cbo_machine_name').value = '".$r_batch[csf("machine_id")]."';\n";
			echo "document.getElementById('cbo_shift_name').value	= '".$r_batch[csf("shift_name")]."';\n";
			echo "document.getElementById('txt_remarks').value	= '".$r_batch[csf("remarks")]."';\n";
			if($r_batch[csf("id")]!="")
			{
				echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',1);\n"; 	
			}
			else
			{
				echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',0);\n"; 	
			}*/
		}
		exit();
	}
}
if($action=='show_fabric_desc_listview')
{	
	$ex_data=explode('_',$data);
	$batch_id=$ex_data[0];
	//$roll_maintained=$ex_data[1];
	
	$company_id=return_field_value("company_id","pro_batch_create_mst","id ='$batch_id' and is_deleted=0 and status_active=1");
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	//echo $company_id;die;
	$fabric_roll_arr=array();
	/*$rollData=sql_select("select id,roll_no,barcode_no from pro_roll_details where  status_active=1 and is_deleted=0");
	foreach($rollData as $row)
	{
		$fabric_roll_arr[$row[csf('id')]]['roll']=$row[csf('roll_no')];
	}*/
	
	$prodData=sql_select("select id,detarmination_id, item_description, gsm, dia_width from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$fabric_desc_arr[$row[csf('id')]]['desc']=$row[csf('item_description')];
		$fabric_desc_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$fabric_desc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
	}
		
	$sql_result=sql_select("SELECT a.entry_form,b.width_dia_type,b.item_description,b.gsm,b.fin_dia, sum(b.batch_qnty) as batch_qty,b.prod_id,b.barcode_no,b.roll_id,b.roll_no from pro_batch_create_dtls b,pro_batch_create_mst a  where b.mst_id='$batch_id'  and b.width_dia_type in(1,2,3) and a.id=b.mst_id and a.id='$batch_id' and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0  group by b.id,b.item_description,b.gsm,b.fin_dia,b.width_dia_type,b.prod_id,a.entry_form,b.barcode_no,b.roll_id,b.roll_no order by b.item_description asc ");
	$i=1;
	if(($page_upto_id==5 || $page_upto_id>5) && $roll_maintained==1)//if($roll_maintained==1)
	{
		
		$b_qty=0;
		if(count($sql_result)>0)
		{
			$checkBatckRoll = array();
			foreach($sql_result as $row)
			{
				$desc=explode(",",$row[csf('item_description')]);
				if($row[csf('entry_form')]==36)
				{
					$desc=explode(",",$row[csf('item_description')]);
					//print_r($desc);
					$cons_comps=$desc[0].','.$desc[1];
					$gsm=$row[csf('gsm')];
					$dia_width=$row[csf('fin_dia')];
				}
				else
				{
					//$cons_comps='';
					$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
					$cons_comps=$cons_comps_data[0].' '.$cons_comps_data[1];
					//print_r($cons_comps_data);
					/*$z=0;
					foreach($cons_comps_data as $val)
					{
						if($z!=0)
						{
							$cons_comps.=$val." ";
						}
						$z++;
					}*/
					$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
					$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
				}
					$roll_no=return_field_value("roll_no","pro_roll_details","id ='".$row[csf('roll_id')]."' and is_deleted=0 and status_active=1");
					//$roll_no=$fabric_roll_arr[$row[csf('roll_id')]]['roll'];
					
			?>
	    	<tr class="general" id="row_<? echo $i; ?>">
	       		<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked /> &nbsp; &nbsp;<? echo $i; ?></td>
	            <td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? echo $cons_comps; ?>" disabled/></td>
	            <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
	            <td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? echo  $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
	            <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
	            <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
	            </td>
	            <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('roll_no')];//$roll_no;?>" style="width:35px;"/ readonly>
	             <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />
	            </td>
	            <td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('batch_qty')]; ?>" disabled/>
	            	<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
	            	 <input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('barcode_no')];//$barcode;?>"  style="width:65px;" <? echo $readonly; ?> />
	             	<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
					<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
	            </td>
	            <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();"  class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('batch_qty')]; ?>"/></td>
	           
	           <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>
	           <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
	        </tr>
	    	<?
			/*if(!in_array($row[csf('roll_no')], $checkBatckRoll))
			{
				$b_qty+=$row[csf('batch_qty')];	
				$checkBatckRoll[]=$row[csf('roll_no')];	
			}*/
			$b_qty+=$row[csf('batch_qty')];
			$prod_qty+= $row[csf('batch_qty')];
			$i++;
			}
		}
		else
		{ ?>
			<tr class="general" id="row_<? echo $i; ?>">
	       		<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked /> &nbsp; &nbsp;<? echo $i; ?></td>
	            <td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? //echo $cons_comps; ?>" disabled/></td>
	            <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? //echo $gsm; ?>" /></td>
	            <td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? //echo  $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
	            <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? //echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
	            <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? //echo $row[csf('width_dia_type')];?>" readonly />
	            </td>
	            <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" value="<? //echo $row[csf('roll_no')];?>" style="width:35px;"/ readonly>
	            <input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes" style="width:65px;" value="<? //echo $barcode; ?>"/>
	             <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? //echo $row[csf('roll_id')];?>" class="text_boxes_numeric" />
	            </td>
	            <td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? //echo $row[csf('batch_qty')]; ?>" disabled/>
	            <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? //echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
	             <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? //echo $row[csf('prod_id')];?>" />
				<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
	            </td>
	            <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();" class="text_boxes_numeric" style="width:60px;"/></td>
	           <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>
	           <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
	        </tr>
			<? 
		}
		?>
	    <tr>
	        <td colspan="6" align="right"><b style=" float:left">Check/Uncheck<input type="checkbox" style="width:30px" id="allcheckbox" name="allcheckbox[]"  onClick="checkbox_all(this.id);" checked  /></b><b>Sum:</b> <? //echo $b_qty; ?> </td>
	        <td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($b_qty,2,'.',''); ?>" readonly /> </b></td>
	        <td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($prod_qty,2,'.',''); ?>" readonly /></td>
	        <td align="right"></td>           
	        <td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" readonly /> </td>
	     </tr>
	    <?
	}// With Roll End;
	else 
	{
		$sql_result=sql_select("SELECT a.id as mst_id, b.id, b.gsm,b.width_dia_type,b.dia_width,b.const_composition,b.batch_qty,b.production_qty, b.roll_no,b.no_of_roll,b.prod_id,b.rate, b.amount from pro_fab_subprocess_dtls b,pro_fab_subprocess a where a.id=b.mst_id and a.batch_id='$batch_id' and b.entry_page=311111 and a.status_active=1 and a.is_deleted=0 order by a.id desc,b.id asc ");// //Multi Entry-No need excute this query
		$i=1;
		$b_qty=0;
		if(count($sql_result)>0)
		{	
			$checkBatckRoll = array();
			$mst_id=$sql_result[0][csf("mst_id")];
			foreach($sql_result as $row)
			{
				if($mst_id!=$row[csf("mst_id")])break;
				$desc=explode(",",$row[csf('item_description')]);
				/*if($row[csf('entry_form')]==36)
					{
						$desc=explode(",",$row[csf('item_description')]);
						print_r($desc);
						$cons_comps=$desc[0];
						$gsm=$desc[1];
						$dia_width=$desc[2];
					}
					else
					{
						$cons_comps='';
						$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
						print_r($cons_comps_data);
						$z=0;
						foreach($cons_comps_data as $val)
						{
							if($z!=0)
							{
								$cons_comps.=$val." ";
							}
							$z++;
						}
						$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
						$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
					}*/
				?>
		    	<tr class="general" id="row_<? echo $i; ?>">
		        	 <td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked /> &nbsp; &nbsp;<? echo $i; ?></td>
		            <td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? echo $row[csf('const_composition')]; ?>" disabled/></td>
		            <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $row[csf('gsm')]; ?>" /></td>
		            <td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? echo  $row[csf('dia_width')]; //$row[csf('width_dia_type')]; ?>" disabled/></td>
		            <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
		             <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
		            </td>
		             <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes" style="width:35px;" value="<? echo $row[csf('no_of_roll')]; ?>"/>					            </td>
		            <td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('batch_qty')]; ?>" disabled/>
		            	<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
		            	 <input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes" style="width:65px;" value="<? echo $barcode; ?>"/>
		             	<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
						<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('id')];?>" readonly />
		            </td>
		            <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();"  class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('production_qty')]; ?>"/>
		            </td>
		            <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('rate')]; ?>" readonly/></td>
		            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')]; ?>"  readonly/> </td>
		        </tr>
		    	<?

				/*if(!in_array($row[csf('no_of_roll')], $checkBatckRoll))
				{
					$b_qty+=$row[csf('batch_qty')];	
					$checkBatckRoll[] = $row[csf('no_of_roll')];	
				}*/
				 $b_qty+=$row[csf('batch_qty')];
				$prod_qty+= $row[csf('production_qty')];
				$prod_amount+= $row[csf('amount')];
				$i++;
				}
		}
		else
		{
			$result=sql_select("SELECT a.entry_form,b.width_dia_type,b.gsm,b.fin_dia,b.item_description, sum(b.batch_qnty) as batch_qty,count(b.roll_no) as roll_no,b.prod_id from pro_batch_create_dtls b,pro_batch_create_mst a  where b.mst_id='$batch_id'  and b.width_dia_type in(1,2,3) and a.id=b.mst_id and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0 group by a.entry_form,b.width_dia_type,b.item_description,b.gsm,b.fin_dia,b.prod_id order by b.item_description asc");
			foreach($result as $row)
			{
				//$desc=explode(",",$row[csf('item_description')]);
				
				if($row[csf('entry_form')]==36)
					{
						$desc=explode(",",$row[csf('item_description')]);
						//print_r($desc);
						$cons_comps=$desc[0].','.$desc[1];
						$gsm=$row[csf('gsm')];
						$dia_width=$row[csf('fin_dia')];
					}
					else
					{
						//$cons_comps='';
						//$cons_comps_data=explode(",",$fabric_desc_arr[$row[csf('prod_id')]]['desc']);
						$cons_comps_data=explode(",",$row[csf('item_description')]);
						$cons_comps=$cons_comps_data[0].' '.$cons_comps_data[1];
						//print_r($cons_comps_data);
						/*$z=0;
						foreach($cons_comps_data as $val)
						{
							if($z!=0)
							{
								$cons_comps.=$val." ";
							}
							$z++;
						}*/
						$gsm=$fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
						$dia_width=$fabric_desc_arr[$row[csf('prod_id')]]['dia'];
					}
			?>
				<tr class="general" id="row_<? echo $i; ?>">
					<td width="40" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  checked /> &nbsp; &nbsp;<? echo $i; ?></td>
					<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? echo $cons_comps; ?>" disabled/></td>
					<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
					<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? echo  $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td>
					<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]];?>" disabled/>
					<input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
					</td>
					<td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" style="width:35px;" value="<? echo $row[csf('roll_no')];?>"/></td>
					<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('batch_qty')]; ?>" disabled/>
					 <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />

					 <input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>"  value="" />

					 <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $row[csf('prod_id')];?>" />
					 <input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly />
					</td>
					<td><input type="text"  value="<? echo $row[csf('batch_qty')]; ?>"   name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();"  class="text_boxes_numeric" style="width:60px;"/></td>
					<td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" readonly/></td>
					<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;"  readonly/> </td>
				</tr>
			<?
				$b_qty+= $row[csf('batch_qty')];
				$prod_qty+= $row[csf('batch_qty')];
				//$prod_amount+= $row[csf('batch_qty')];
				$i++;
			}	
	 	} 
	 	?>
	  	<tr>
	        <td colspan="6" align="right"><b style=" float:left">Check/Uncheck<input type="checkbox" style="width:30px" id="allcheckbox" name="allcheckbox[]"  onClick="checkbox_all(this.id);" checked  /></b><b>Sum:</b> <? //echo $b_qty; ?> </td>
	        <td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($b_qty,2,'.',''); ?>" readonly /> </b></td>
	        <td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($prod_qty,2,'.',''); ?>" readonly /></td>
	        <td align="right"></td>           
	        <td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($prod_amount,2,'.',''); ?>" readonly /> </td>
	        </tr>
	 	
		<? 
	}
	
	exit();
}

if($action=="save_update_delete")
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
		$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$cbo_company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
		if(str_replace("'","",$update_id)=="")
		{
		$field_array="id,company_id,width_dia_type,batch_no,re_stenter_no,batch_id,batch_ext_no,process_id,production_date,process_start_date,start_hours,start_minutes,end_hours,end_minutes,machine_id,result,floor_id,entry_form,temparature,stretch,over_feed,feed_in,pinning,speed_min,process_end_date,chemical_name,shift_name,remarks,issue_chalan,service_source,service_company,received_chalan,issue_challan_mst_id,booking_no,previous_process,inserted_by,insert_date";
		$id=return_next_id( "id", " pro_fab_subprocess", 1 ) ;
		$data_array="(".$id.",".$cbo_company_id.",".$cbo_dia_type.",".$txt_batch_no.",".$txt_restenter_no.",".$txt_batch_ID.",".$txt_ext_id.",".$txt_process_id.",".$txt_process_date.",".$txt_process_start_date.",".$txt_start_hours.",".$txt_start_minutes.",".$txt_end_hours.",".$txt_end_minutes.",".$cbo_machine_name.",".$cbo_result_name.",".$cbo_floor.",31,".$txt_temparature.",".$txt_stretch.",".$txt_feed.",".$txt_feed_in.",".$txt_pinning.",".$txt_speed.",".$txt_process_end_date.",".$txt_chemical.",".$cbo_shift_name.",".$txt_remarks.",".$txt_issue_chalan.",".$cbo_service_source.",".$cbo_service_company.",".$txt_recevied_chalan.",".$txt_issue_mst_id.",".$txt_booking_no.",".$cbo_previous_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//print_r($data_array);die;
		//echo "insert into pro_fab_subprocess ($field_array) values $data_array";die;
		$mst_update_id=str_replace("'","",$id);
		}
		//if(str_replace("'","",$roll_maintained)==1)
		if(($page_upto_id==5 || $page_upto_id>5) && str_replace("'","",$roll_maintained)==1)
		{
			$field_array_dtls="id,mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type, batch_qty,roll_no,barcode_no,roll_id,production_qty, rate, amount, currency_id,exchange_rate,inserted_by,insert_date";
		}
		else
		{
			$field_array_dtls="id,mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty, no_of_roll,production_qty, rate, amount, currency_id, exchange_rate,inserted_by,insert_date";	
		}
		
		$id_dtls=return_next_id( "id", "pro_fab_subprocess_dtls", 1 ) ;
		//if(str_replace("'","",$roll_maintained)==1)
		if(($page_upto_id==5 || $page_upto_id>5) && str_replace("'","",$roll_maintained)==1)
		{
			//echo $total_row;die;
			for($i=1;$i<=$total_row;$i++)
			{
				$checked_tr="checkRow_".$i;
				if(str_replace("'","",$$checked_tr)==1)
				{
					$prod_id="txtprodid_".$i;
					$txtconscomp="txtconscomp_".$i;
					$txtgsm="txtgsm_".$i;
					$txtroll="txtroll_".$i;
					$rollid="rollid_".$i;
					$txtbarcode="txtbarcode_".$i;
					$txtproductionqty="txtproductionqty_".$i;
					$txtbodypart="txtbodypart_".$i;
					$txtdiawidth="txtdiawidth_".$i;
					$txtbatchqnty="txtbatchqnty_".$i;
					$txtdiawidthID="txtdiawidthID_".$i;
					$txtrate="txtrate_".$i;
					$txtamount="txtamount_".$i;
					$Itemprod_id=str_replace("'","",$$prod_id);
					if($data_array_dtls!="") $data_array_dtls.=","; 
					$data_array_dtls.="(".$id_dtls.",".$mst_update_id.",31,".$Itemprod_id.",".$$txtconscomp.",".$$txtgsm.",".$$txtbodypart.",".$$txtdiawidthID.",".$$txtbatchqnty.",".$$txtroll.",".$$txtbarcode.",".$$rollid.",".$$txtproductionqty.",".$$txtrate.",".$$txtamount.",".$hidden_currency.",".$hidden_exchange_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls=$id_dtls+1;
				}
			}
		}
		else
		{
			for($i=1;$i<=$total_row;$i++)
			{
				$checked_tr="checkRow_".$i;
				if(str_replace("'","",$$checked_tr)==1)
				{
					$prod_id="txtprodid_".$i;
					$txtconscomp="txtconscomp_".$i;
					$txtgsm="txtgsm_".$i;
					$txtroll="txtroll_".$i;
					$txtproductionqty="txtproductionqty_".$i;
					$txtbodypart="txtbodypart_".$i;
					$txtdiawidth="txtdiawidth_".$i;
					$txtbatchqnty="txtbatchqnty_".$i;
					$txtdiawidthID="txtdiawidthID_".$i;
					$txtrate="txtrate_".$i;
					$txtamount="txtamount_".$i;
					$Itemprod_id=str_replace("'","",$$prod_id);
					if($data_array_dtls!="") $data_array_dtls.=","; 
					$data_array_dtls.="(".$id_dtls.",".$mst_update_id.",31,".$Itemprod_id.",".$$txtconscomp.",".$$txtgsm.",".$$txtbodypart.",".$$txtdiawidthID.",".$$txtbatchqnty.",".$$txtroll.",".$$txtproductionqty.",".$$txtrate.",".$$txtamount.",".$hidden_currency.",".$hidden_exchange_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls=$id_dtls+1;
				}
			}	
		}
		// echo "insert into pro_fab_subprocess ($field_array) values $data_array";die;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("pro_fab_subprocess",$field_array,$data_array,0);
		}
		$rID2=sql_insert("pro_fab_subprocess_dtls",$field_array_dtls,$data_array_dtls,0);

		$batch_field_array_update = "total_trims_weight*updated_by*update_date";
		$batch_data_array_update = "".$txt_trims_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID3=sql_update("pro_batch_create_mst",$batch_field_array_update,$batch_data_array_update,"id",$txt_batch_ID,1);
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_update_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{	
			
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".$mst_update_id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$mst_update_id;
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
		$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$cbo_company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
		
		$update_id=str_replace("'","",$txt_update_id);
		$field_array_update="company_id*width_dia_type*batch_no*re_stenter_no*service_source*service_company*received_chalan*issue_chalan*issue_challan_mst_id*batch_id*batch_ext_no*process_id*production_date*process_start_date*start_hours*start_minutes*end_hours*end_minutes*machine_id*result*floor_id*entry_form*temparature*stretch*over_feed*feed_in*pinning*speed_min*process_end_date*chemical_name*shift_name*remarks*booking_no*previous_process*updated_by*update_date";
		$data_array_update="".$cbo_company_id."*".$cbo_dia_type."*".$txt_batch_no."*".$txt_restenter_no."*".$cbo_service_source."*".$cbo_service_company."*".$txt_recevied_chalan."*".$txt_issue_chalan."*".$txt_issue_mst_id."*".$txt_batch_ID."*".$txt_ext_id."*".$txt_process_id."*".$txt_process_date."*".$txt_process_start_date."*".$txt_start_hours."*".$txt_start_minutes."*".$txt_end_hours."*".$txt_end_minutes."*".$cbo_machine_name."*".$cbo_result_name."*".$cbo_floor."*31*".$txt_temparature."*".$txt_stretch."*".$txt_feed."*".$txt_feed_in."*".$txt_pinning."*".$txt_speed."*".$txt_process_end_date."*".$txt_chemical."*".$cbo_shift_name."*".$txt_remarks."*".$txt_booking_no."*".$cbo_previous_process."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r($data_array_update);die;
		$flag=0;
		$rID=sql_update("pro_fab_subprocess",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		
		//$field_array_dtls="id, mst_id, entry_page, prod_id, const_composition, gsm, dia_width, width_dia_type, batch_qty, inserted_by, insert_date";
		$add_comma=0;
		//if(str_replace("'","",$roll_maintained)==1)
		if(($page_upto_id==5 || $page_upto_id>5) && str_replace("'","",$roll_maintained)==1)
		{
		  	$field_array_up="mst_id*gsm*production_qty*roll_no*barcode_no*roll_id*rate*amount*currency_id*exchange_rate*updated_by*update_date";
		  	$field_array_delete="updated_by*update_date*status_active*is_deleted";
		   	for($i=1; $i<=$total_row; $i++)
			{
				$checkRowTd="checkRow_".$i;
				$prod_id="txtprodid_".$i;
				//$txtconscomp="txtconscomp_".$i;
				$txtgsm="txtgsm_".$i;
				$txtroll="txtroll_".$i;
				$rollid="rollid_".$i;
				$txtbarcode="txtbarcode_".$i;
				$txtproductionqty="txtproductionqty_".$i;
				$txtrate="txtrate_".$i;
				$txtamount="txtamount_".$i;
				//$txtbodypart="txtbodypart_".$i;
				$txtdiawidth="txtdiawidth_".$i;
				//$txtbatchqnty="txtbatchqnty_".$i;
				$updateiddtls="updateiddtls_".$i;
				if(str_replace("'","",$$checkRowTd)==1)
				{
					$id_arr[]=str_replace("'",'',$$updateiddtls);
					$data_array_up[str_replace("'",'',$$updateiddtls)] =explode("*",("".$update_id."*".$$txtgsm."*".$$txtproductionqty."*".$$txtroll."*".$$txtbarcode."*".$$rollid."*".$$txtrate."*".$$txtamount."*".$hidden_currency."*".$hidden_exchange_rate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
				else
				{
					$id_arr_delete[]=str_replace("'",'',$$updateiddtls);
					$data_array_delete[str_replace("'",'',$$updateiddtls)] =explode("*",("'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));	
				}
			}
		}
		else
		{
			$field_array_up="mst_id*gsm*production_qty*no_of_roll*rate*amount*currency_id*exchange_rate*updated_by*update_date";
			$field_array_delete="updated_by*update_date*status_active*is_deleted";
			for($i=1; $i<=$total_row; $i++)
			{
				$prod_id="txtprodid_".$i;
				//$txtconscomp="txtconscomp_".$i;
				$txtgsm="txtgsm_".$i;
				$txtroll="txtroll_".$i;
				$rollid="rollid_".$i;
				$txtproductionqty="txtproductionqty_".$i;
				$txtrate="txtrate_".$i;
				$txtamount="txtamount_".$i;
				//$txtbodypart="txtbodypart_".$i;
				$txtdiawidth="txtdiawidth_".$i;
				//$txtbatchqnty="txtbatchqnty_".$i;
				$updateiddtls="updateiddtls_".$i;
				$checked_tr="checkRow_".$i;
				if(str_replace("'","",$$checked_tr)==1)
				{
					$id_arr[]=str_replace("'",'',$$updateiddtls);
					$data_array_up[str_replace("'",'',$$updateiddtls)] =explode("*",("".$update_id."*".$$txtgsm."*".$$txtproductionqty."*".$$txtroll."*".$$txtrate."*".$$txtamount."*".$hidden_currency."*".$hidden_exchange_rate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));	
				}
				else
				{
					$id_arr_delete[]=str_replace("'",'',$$updateiddtls);
					$data_array_delete[str_replace("'",'',$$updateiddtls)] =explode("*",("'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));	
				}
				
			}
			
		}
		if(count($data_array_up)>0)
			{
				$rID2=execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID2) $flag=1; else $flag=20;
			}
			if(count($data_array_delete)>0)
			{
				$rID3=execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id",$field_array_delete,$data_array_delete,$id_arr_delete ));
				if($rID3) $flag=1; else $flag=20;
			}
		
		//echo "insert into pro_fab_subprocess values $field_array_update,$data_array_update,'id',$update_id,0)";die;
		//check_table_status( $_SESSION['menu_id'],0);
		
		$batch_field_array_update = "total_trims_weight*updated_by*update_date";
		$batch_data_array_update = "".$txt_trims_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID4=sql_update("pro_batch_create_mst",$batch_field_array_update,$batch_data_array_update,"id",$txt_batch_ID,1);	
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{	
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id;
			}
		}
		disconnect($con);
		die;
	}
}
if($action=="check_batch_no")
{ //and company_id='".trim($data[0])."' 
	$data=explode("**",$data);
	$sql="select id, batch_no, is_sales from pro_batch_create_mst where batch_no='".trim($data[1])."' and entry_form in(0,36) and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('is_sales')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}
if($action=="check_batch_no_scan")
{
	$data=explode("**",$data);
	$batch_id=$data[1];//(int)
	$sql="select id, batch_no,company_id from pro_batch_create_mst where id='".$batch_id."' and entry_form in(0,36)  and is_deleted=0 and status_active=1 order by id desc";
	$data_array=sql_select($sql,1);
	echo $data_array[0][csf('batch_no')];
	/*if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('company_id')];
	}
	else
	{
		echo "0_";
	}*/
	exit();	
}
if($action=="process_name_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
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
		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
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
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
                    $i=1; $process_row_id=''; $not_process_id_print_array=array(90,93,194,242);
					$hidden_process_id=explode(",",$txt_process_id);
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						if(in_array($id,$not_process_id_print_array))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if(in_array($id,$hidden_process_id)) 
							{ 
								if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
								</td>	
								<td><p><? echo $name; ?></p></td>
							</tr>
							<?
							$i++;
						}
                    }
                ?>
                <input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}
if($action=="show_dtls_list_view")
{

	//$ex_data = explode("**",$data);
	//$issue_number = $ex_data[0];
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$company_id=return_field_value("company_id","pro_batch_create_mst","id ='$data' and is_deleted=0 and status_active=1");
	
	if($db_type==2)
	{
	 $sql = "SELECT a.re_stenter_no,a.id,listagg(cast(b.const_composition as varchar2(4000)),',') within group (order by b.const_composition) AS const_composition,listagg(b.gsm,',') within group (order by b.gsm ) as gsm ,listagg(cast(b.roll_no as varchar2(4000)),',') within group (order by b.roll_no) AS roll_no,listagg(b.dia_width,',') within group (order by b.dia_width ) as dia_width,listagg(b.width_dia_type,',') within group (order by b.width_dia_type ) as width_dia_type,listagg(b.roll_id,',') within group (order by b.roll_id ) as roll_id,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty,sum(b.no_of_roll) as no_of_roll from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$data and a.entry_form=31 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  group by a.re_stenter_no,a.id";
	}
	else
	{
	$sql = "SELECT a.re_stenter_no,a.id,group_concat(b.const_composition) AS const_composition,group_concat(b.gsm) as gsm,group_concat(b.roll_no) as roll_no ,group_concat(b.dia_width)  as dia_width,group_concat(b.width_dia_type)  as width_dia_type,group_concat(b.roll_id) as roll_id,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty,sum(b.no_of_roll) as no_of_roll from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$data and a.entry_form=31 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  group by a.re_stenter_no,a.id";	
	}
	$result = sql_select($sql);
	$i=1;
	$total_batch_qty=0;
	$total_prod_qty=0;
	?> 
    	
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="800" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Cons Composition</th>
                    <th>GSM</th>                    
                    <th>Dia/Width</th>
                    <th>Dia Width Type</th>
                    <th>Re Dryer No.</th>
                    <th>Bacth Qty</th>
                    <th>Prod. Qty</th>
                </tr>
            </thead>
            <tbody>
            	<?
            	$checkBatch = array();
            	 foreach($result as $row){  
					
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					
						$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
 
					if(!in_array($row[csf('batch_id')], $checkBatch))
					{
						$total_batch_qty +=	$row[csf("batch_qty")];
						$checkBatch[] =	$row[csf('batch_id')];
					}
					
					$total_prod_qty +=	$row[csf("production_qty")];
					
					
					$dia_type='';
					$dia_type_id=array_unique(explode(",",$row[csf('width_dia_type')]));
					foreach($dia_type_id as $dia_id)
					{	
						
						if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
					}
					$cons_composition_cond='';
					$cons_composition_arr=array_unique(explode(",",$row[csf('const_composition')]));
					foreach($cons_composition_arr as $cons)
					{	
						if($cons_composition_cond=="") $cons_composition_cond=$cons; else $cons_composition_cond.=",".$cons;
					}
					$gsm_cond='';
					$gsm_cond_arr=array_unique(explode(",",$row[csf('gsm')]));
					foreach($gsm_cond_arr as $gsm)
					{	
						if($gsm_cond=="") $gsm_cond=$gsm; else $gsm_cond.=",".$gsm;
					}
					$dia_width_cond='';
					$gsm_cond_arr=array_unique(explode(",",$row[csf('dia_width')]));
					foreach($gsm_cond_arr as $dia)
					{	
						if($dia_width_cond=="") $dia_width_cond=$dia; else $dia_width_cond.=",".$dia;
					}
					if($roll_maintained==1)
					{
					$roll_no=$row[csf("roll_no")];
					
					}
					else
					{
					$roll_no=$row[csf("no_of_roll")];
					}
								?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='show_list_view("<? echo $row[csf("id")];?>","child_form_input_data","list_fabric_desc_container","requires/drying_stentering_controller");get_php_form_data("<? echo $row[csf("id")];?>","mst_id_child_form_input_data","requires/drying_stentering_controller")'  style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="80"><p><? echo $cons_composition_cond; ?></p></td>
                        <td width="80"><p><? echo $gsm_cond; ?></p></td>
                        <td width="70"><p><? echo $dia_width_cond; ?></p></td>
                        <td width="130"><p><? echo   $dia_type;//$row[csf("width_dia_type")] ; ?></p></td>
                        <td width="100"><p><? echo $row[csf('re_stenter_no')];  ?></p></td>
                         <td width="80" align="right"><p><? echo number_format($row[csf("batch_qty")],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("production_qty")],2); ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                            <th colspan="6" align="right">Sum</th>
                            <th><? echo number_format($total_batch_qty,2); ?></th><th><? echo number_format($total_prod_qty,2); ?></th>
                     </tfoot>
            </tbody>
        </table>
    <?
	exit();

}
if($action=="child_form_input_data")
{
	
	//print($data);
	//$ex_data=explode('_',$data);
	//$batch_id=$ex_data[0];
	$company_id=return_field_value("company_id","pro_fab_subprocess","id=$data and is_deleted=0 and status_active=1");
	$fabric_desc_arr=array();
	$prodData=sql_select("select id,detarmination_id from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
	}
	$sql_result=sql_select("select a.id,b.id as dtls_id,b.barcode_no,b.prod_id,b.const_composition,b.gsm,b.dia_width,b.width_dia_type,b.batch_qty,b.production_qty,b.no_of_roll,b.roll_no,b.roll_id,b.rate,b.amount from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.id=$data and a.entry_form=31 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
		$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	$i=1;
	foreach($sql_result as $row)
	{
		//$desc=explode(",",$row[csf('item_description')]);
		
		
			$cons_comps=$row[csf('const_composition')];
			$gsm=$row[csf('gsm')];
			$dia_width=$row[csf('dia_width')];
			$width_dia_type=$row[csf('width_dia_type')];
			$batch_qty=$row[csf('batch_qty')];
			$production_qty=$row[csf('production_qty')];
			$roll_no=$row[csf('roll_no')];
			$roll_id=$row[csf('roll_id')];
			$prod_id=$row[csf('prod_id')];
			$update_id=$row[csf('dtls_id')];
			if($roll_maintained==1)
			{
				$roll_no=$row[csf('roll_no')];
				$readonly="readonly";	
			}
			else
			{
				$roll_no=$row[csf('no_of_roll')];
				$readonly="";
			}
			?>
		<tr class="general" id="row_<? echo $i; ?>">
         	<td width="40" id="sl_<? echo $i; ?>">
         	 <? 
			//if(($page_upto_id==5 || $page_upto_id>5) && $roll_maintained==1) { ?> 
			<input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow_<? echo $i; ?>" checked >  
			<? //}
			 ?>
         	 &nbsp; &nbsp;<? echo $i; ?></td>
			<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:180px;" value="<? echo $cons_comps; ?>" disabled/></td>
			<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $gsm; ?>" /></td>
			<td><input type="text" name="txtbodypart_<? echo $i; ?>" id="txtbodypart_<? echo $i; ?>" class="text_boxes" style="width:50px;" value="<? echo  $dia_width; //$row[csf('width_dia_type')]; ?>" disabled/></td> 
			<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:80px;" value="<? echo $fabric_typee[$width_dia_type];?>" disabled/>
             <input type="hidden" name="txtdiawidthID_<? echo $i; ?>" id="txtdiawidthID_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')];?>" readonly />
            </td>
             <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $roll_no;?>" style="width:35px;"  <? echo $readonly; ?> /> <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $roll_id;?>" class="text_boxes_numeric" />	</td>
			<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $batch_qty; ?>" disabled/>
				<input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('barcode_no')];//$barcode;?>" style="width:65px;" <? echo $readonly; ?>  /> 
            	 <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id'];?>" />
			   	<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"  value="<? echo $prod_id;?>" />
			 	<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" style="width:60px;" value="<? echo $row[csf('dtls_id')];?>" readonly />
			</td>
            <td><input type="text" name="txtproductionqty_<? echo $i; ?>" id="txtproductionqty_<? echo $i; ?>" onKeyUp="calculate_production_qnty();"  value="<? echo $production_qty; ?>" class="text_boxes_numeric" style="width:60px;"/></td>
            <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('rate')]; ?>" readonly/></td>
            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')]; ?>"  readonly/> </td>
		</tr>
		<?
		//$b_qty+= $batch_qty;
	
		$tot_batch_qty+= $batch_qty;
		$tot_production_qty+= $production_qty;
		$tot_amount+= $row[csf('amount')];
	
		$i++;
		
	}
	?>
	  
    <tr>
        <td colspan="6" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
        <td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($tot_batch_qty); ?>" readonly /> </b></td>
        <td><input type="text" name="total_production_qnty" id="total_production_qnty" value="<? echo number_format($tot_production_qty); ?>" class="text_boxes_numeric" style="width:60px" readonly /></td>
        <td align="right"></td>           
        <td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" value="<? echo $tot_amount; ?>" readonly /> </td> 
        
     </tr>
	<?
	exit();
	

}
//For Master ID update
if($action=="mst_id_child_form_input_data")
{
	$sql_result=sql_select("SELECT a.re_stenter_no,a.id,a.company_id,a.batch_id,a.service_source,a.service_company,a.received_chalan,a.issue_chalan,issue_challan_mst_id, a.process_end_date,a.production_date,a.process_start_date,a.previous_process,a.process_id,a.end_hours,a.end_minutes,a.start_hours,a.temparature,a.stretch,a.over_feed,a.feed_in,a.pinning,a.speed_min,a.floor_id,a.machine_id,a.result,a.shift_name,a.remarks,a.booking_no,b.currency_id,b.exchange_rate from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.id=$data and entry_form=31 ");
	  
	$variable_production_roll=return_field_value("fabric_roll_level", "variable_settings_production", " variable_list=3 and item_category_id=50 and status_active=1 and is_deleted=0 and company_name=".$sql_result[0][csf('company_id')]."");
  	$trims_weight=return_field_value("total_trims_weight","pro_batch_create_mst","company_id =".$sql_result[0][csf('company_id')]." and id=".$sql_result[0][csf('batch_id')]." and is_deleted=0 and status_active=1");

	$process_name_val='';
	$process_id_array=explode(",",$sql_result[0][csf("process_id")]);
	// echo $sql_batch[0][csf("process_id")].'aziz';
	foreach($process_id_array as $val)
	{
		if($process_name_val=="") $process_name_val=$conversion_cost_head_array[$val]; else $process_name_val.=",".$conversion_cost_head_array[$val];
	}
	if($sql_result[0][csf("floor_id")]==""){$floor_id=0;}else{$floor_id=$sql_result[0][csf("floor_id")];}
	echo "document.getElementById('txt_issue_chalan').value	= '".$sql_result[0][csf('issue_chalan')]."';\n";
	echo "document.getElementById('txt_issue_mst_id').value	= '".$sql_result[0][csf('issue_challan_mst_id')]."';\n";
	echo "document.getElementById('cbo_service_source').value	= ".$sql_result[0][csf('service_source')].";\n";
	echo "document.getElementById('cbo_company_id').value	= '".$sql_result[0][csf('company_id')]."';\n";
	echo "document.getElementById('txt_restenter_no').value	= '".$sql_result[0][csf('re_stenter_no')]."';\n";
	echo "document.getElementById('txt_recevied_chalan').value	= '".$sql_result[0][csf('received_chalan')]."';\n";
	echo "document.getElementById('roll_maintained').value	= '". $variable_production_roll."';\n";
	echo "document.getElementById('txt_process_end_date').value 	= '".change_date_format($sql_result[0][csf("process_end_date")])."';\n";
	echo "document.getElementById('txt_process_date').value 	= '".change_date_format($sql_result[0][csf("production_date")])."';\n";
	echo "document.getElementById('txt_process_start_date').value 	= '".change_date_format($sql_result[0][csf("process_start_date")])."';\n";
	echo "document.getElementById('txt_trims_weight').value 	= '".$trims_weight."';\n";
	
	//echo $process_name;
	echo "document.getElementById('txt_process_name').value 	= '".$process_name_val."';\n";
	$minute=''; $hour='';
	if ($sql_result[0][csf("end_hours")] != '' && $sql_result[0][csf("end_minutes")] != '')
	{
		$hour=str_pad($sql_result[0][csf("end_hours")],2,'0',STR_PAD_LEFT);
	    $minute=str_pad($sql_result[0][csf("end_minutes")],2,'0',STR_PAD_LEFT);
	}	
	echo "document.getElementById('txt_end_hours').value	= '".$hour."';\n";
	echo "document.getElementById('txt_end_minutes').value	= '".$minute."';\n";
	$start_hour=str_pad($sql_result[0][csf("start_hours")],2,'0',STR_PAD_LEFT);
	$start_minute=str_pad($sql_result[0][csf("start_minutes")],2,'0',STR_PAD_LEFT);
	echo "document.getElementById('txt_start_hours').value	= '".$start_hour."';\n";
	echo "document.getElementById('txt_start_minutes').value = '".$start_minute."';\n";
	
	echo "document.getElementById('txt_temparature').value	= '".$sql_result[0][csf("temparature")]."';\n";
	echo "document.getElementById('txt_stretch').value	= '".$sql_result[0][csf("stretch")]."';\n";
	echo "document.getElementById('txt_feed').value	= '".$sql_result[0][csf("over_feed")]."';\n";
	echo "document.getElementById('txt_feed_in').value	= '".$sql_result[0][csf("feed_in")]."';\n";
	echo "document.getElementById('txt_pinning').value	= '".$sql_result[0][csf("pinning")]."';\n";
	echo "document.getElementById('txt_speed').value	= '".$sql_result[0][csf("speed_min")]."';\n";
	if($sql_result[0][csf('service_company')]==1)
	{
	echo "load_drop_down('requires/drying_stentering_controller', '".$sql_result[0][csf('service_company')]."', 'load_drop_floor', 'floor_td' );\n";
	}
	
	echo "load_drop_down( 'requires/drying_stentering_controller', document.getElementById('cbo_service_company').value+'**'+".$floor_id.", 'load_drop_machine', 'machine_td' );\n";
	
	echo "document.getElementById('cbo_floor').value = '".$floor_id."';\n";
	echo "document.getElementById('cbo_machine_name').value = '".$sql_result[0][csf("machine_id")]."';\n";
	echo "document.getElementById('cbo_result_name').value = '".$sql_result[0][csf("result")]."';\n";
	echo "document.getElementById('cbo_previous_process').value = '".$sql_result[0][csf("previous_process")]."';\n";
	echo "document.getElementById('cbo_shift_name').value	= '".$sql_result[0][csf("shift_name")]."';\n";
	echo "document.getElementById('txt_remarks').value	= '".$sql_result[0][csf("remarks")]."';\n";
	echo "$('#cbo_service_source').val(".$sql_result[0][csf("service_source")].");\n";
	echo "load_drop_down( 'requires/drying_stentering_controller', ".$sql_result[0][csf("service_source")]."+'**'+".$sql_result[0][csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
	echo "$('#cbo_service_company').val('".$sql_result[0][csf("service_company")]."');\n";
	echo "document.getElementById('txt_booking_no').value	= '".$sql_result[0][csf("booking_no")]."';\n";
	echo "document.getElementById('hidden_currency').value	= '".$sql_result[0][csf("currency_id")]."';\n";
	echo "document.getElementById('hidden_exchange_rate').value	= '".$sql_result[0][csf("exchange_rate")]."';\n";
	echo "document.getElementById('txt_update_id').value	= ".$data.";\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',1,1);\n";
//echo "set_button_status(1, permission, 'fnc_pro_fab_subprocess',1,1);\n";
	exit();
}


if ($action=="service_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
     
	<script>
	var permission="<? echo $_SESSION['page_permission']; ?>";
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="900">
            <thead>
            	<tr>
                    <th colspan="3"> </th>
                    <th>
                      <?
                       echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                      ?>
                    </th>
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <th width="160">Company Name</th>
                    <th width="160">Buyer Name</th>
                    <th width="120">Booking No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" id="rst" class="formbutton" style="width:100px" onClick="reset_form('searchorderfrm_1','search_div','','','')" ></th>   
                </tr>                	 
            </thead>
            <tbody>
                <tr>
                    <td align="center"> <input type="hidden" id="selected_booking">
                    <? 
                   		echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and id=".$cbo_company_id." order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'drying_stentering_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                    </td>
                    <td id="buyer_td"  align="center">
                    <? 
                    	echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );
                    ?>	
                    </td>
                    <td align="center">
                   		<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px"  placeholder="Write Booking No">	
                    </td>
                    <td  align="center">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td> 
                    <td align="center">
                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+'<?php echo $supplier_id."_".$process_id; ?>', 'create_booking_search_list_view', 'search_div', 'drying_stentering_controller', 'setFilterGrid(\'table_body\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                	<td  align="center" height="40" valign="middle" colspan="6"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </tbody>
        </table>   
    	
    </form>
    </div>
    <div id="search_div" style="margin-top:10px;"> </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$date_form=$data[2];
	$date_to=$data[3];
	$search_catgory=$data[4];
	$booking_no=$data[5];
	$job_no=$data[6];
	$supplier_id=$data[6];
	$process_id=$data[7];
	$sql_cond="";
	if ($company_id!=0) $sql_cond =" and a.company_id='$company_id'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0) $sql_cond .=" and a.buyer_id='$buyer_id'";
	
	if($db_type==0)
	{
		if ($date_form!="" &&  $date_to!="")  $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
	}
	if($db_type==2)
	{
		if ($date_form!="" &&  $date_to!="") $sql_cond .= "and a.booking_date  between '".change_date_format($date_form, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'";
	}
	
	
	if($booking_no!="") $sql_cond .=" and a.booking_no_prefix_num=$booking_no";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$job_no_arr=return_library_array( "select b.id, a.job_no_prefix_num from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst",'id','job_no_prefix_num');
	$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	
	$sql_booking= sql_select("select f.lib_yarn_count_deter_id,d.pre_cost_fabric_cost_dtls_id,sum(d.amount) as amount, sum(d.wo_qnty) as wo_qnty,d.booking_no  from wo_pre_cost_fab_conv_cost_dtls e,wo_pre_cost_fabric_cost_dtls f, wo_booking_dtls d,wo_booking_mst a, wo_po_details_master b where e.job_no=f.job_no and f.id=e.fabric_description and e.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and a.job_no=b.job_no and a.booking_type=3 and a.supplier_id=$supplier_id and  a.status_active=1 and a.is_deleted=0 and d.process in ($process_id) $sql_cond group by d.booking_no,d.pre_cost_fabric_cost_dtls_id,f.lib_yarn_count_deter_id ");
	$booking_determination_rate=array();
	foreach($sql_booking as $val)
	{
		$booking_determination_rate[$val[csf('booking_no')]][$val[csf('lib_yarn_count_deter_id')]]['wo_qnty']+=$val[csf('wo_qnty')];
		$booking_determination_rate[$val[csf('booking_no')]][$val[csf('lib_yarn_count_deter_id')]]['amount']+=$val[csf('amount')];
	}
	//print_r($booking_determination_rate);
	$sql= "select   sum(d.amount)/ sum(d.wo_qnty) as rate,a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num,a.currency_id, a.exchange_rate  from wo_booking_dtls d,wo_booking_mst a, wo_po_details_master b where d.booking_no=a.booking_no and a.job_no=b.job_no and a.booking_type=3 and a.supplier_id=$supplier_id and  a.status_active=1 and a.is_deleted=0 and d.process in ($process_id) $sql_cond group by a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num ,a.currency_id, a.exchange_rate order by a.booking_no"; 
	//echo $sql;
	?>
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970">
    	<thead>
        	<tr>
            	<th width="40">SL</th>
            	<th width="50">Booking No</th>
                <th width="70">Booking Date</th>
                <th width="60">Company</th>
                <th width="60">Buyer</th>
                <th width="60">Job No</th>
                <th width="200">PO number</th>
                <th width="120">Item Category</th>
                <th width="110">Fabric Source</th>
                <th>Supplier</th>  
            </tr>
        </thead>
    </table>
    <div id="scroll_body" style="width:990px; max-height:350; overflow-y:scroll" align="center">
    <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970" id="table_body">
        <tbody>
        <?
		$sql_result=sql_select($sql);
		$i=1;
		foreach($sql_result as $row)
		{
			$determination_data='';
			foreach($booking_determination_rate[$row[csf("booking_no")]] as $deter_id=>$deter_val)
			{
				$determination_data.=$deter_id."*".$deter_val['amount']/$deter_val['wo_qnty']."**";
			}
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]."_".$row[csf("currency_id")]."_".$row[csf("exchange_rate")]."_".$determination_data; ?>')" style="cursor:pointer;">
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><p><? echo $row[csf("booking_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? if($row[csf("booking_date")]!="" && $row[csf("booking_date")]!="0000-00-00") echo change_date_format($row[csf("booking_date")]); ?>&nbsp;</p></td>
                <td width="60"><p><? echo $comp_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
                <td width="60"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                <td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="200"><p>
				<?
				$po_id_arr=array_unique(explode(",",$row[csf("po_break_down_id")]));
				$all_po="";
				foreach($po_id_arr as $po_id)
				{
					$all_po.=$po_no_arr[$po_id].",";
				}
				$all_po=chop($all_po," , ");
				echo $all_po; 
				?>&nbsp;</p></td>
                <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                <td width="110"><p><? echo $fabric_source[$row[csf("fabric_source")]]; ?>&nbsp;</p></td>
                <td><p><? echo $suplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>  
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <?
}



?>