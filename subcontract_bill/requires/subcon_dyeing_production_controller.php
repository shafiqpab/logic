<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0 and color_name is not null",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
$machine_name=return_library_array( "select machine_no,id from  lib_machine_name where is_deleted=0", "id", "machine_no");
$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
$brand_name=return_library_array( "select id, brand_name from   lib_brand",'id','brand_name');

if ($action=="load_drop_floor")
{
	$data=explode('_',$data);
	$loca=$data[0];
	$com=$data[1];
	//echo "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]'  order by floor_name";die;
	echo create_drop_down( "cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]'  and production_process=3  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected,"load_drop_down( 'requires/subcon_dyeing_production_controller', document.getElementById('cbo_company_id').value+'**'+this.value, 'load_drop_machine', 'machine_td' );" );     	 
	exit();
}

if($action=="load_drop_sub_process")
{
	echo create_drop_down( "cbo_sub_process", 135, $conversion_cost_head_array,"", 0, "", $selected, "","",$data );
}

if ($action=="load_drop_machine")
{
	 
	$data=explode('**',$data);
	$com=$data[0];
	$floor=$data[1];
	//$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor where id=$floor",'id','floor_name');
	if($db_type==2)
	{
		echo create_drop_down( "cbo_machine_name", 135, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and 				company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name","id,machine_name", 1,"-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value, 
	'populate_data_from_machine', 'requires/subcon_dyeing_production_controller' );","" );
	}
	else if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 135, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name","id,machine_name", 1, "-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value, 'populate_data_from_machine', 'requires/subcon_dyeing_production_controller' );","" );
	}
	exit();
}

if ($action=="populate_data_from_machine")
{ 
 
	$ex_data=explode('**',$data);
	$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor where   id=$ex_data[1]",'id','floor_name');
	$sql_res="select id, floor_id, machine_group from lib_machine_name where id=$ex_data[2] and category_id=2 and company_id=$ex_data[0] and  floor_id=$ex_data[1] and status_active=1 and is_deleted=0";
	$nameArray=sql_select($sql_res);
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_machine_no').value 			= '".$floor_arr[$row[csf("floor_id")]]."';\n";
		echo "document.getElementById('txt_mc_group').value 			= '".$row[csf("machine_group")]."';\n";
	}
	exit();
}

if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../", 1, 1,'','','');
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
        <fieldset style="width:770px;">
        <legend>Enter search words</legend>
             <table cellpadding="0" cellspacing="0" width="760" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="4">
                          <?
							  echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                          ?>
                        </th>
                    </tr>                	
                    <tr align="center">
                        <th width="150px">Batch No</th>
                        <th width="220px">Batch Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
                        </th>
                    </tr>
                </thead>
                <tr align="center">
                     <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_batch" id="txt_search_batch" />	
                    </td> 
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
                    </td>
                   
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_batch_search_list_view', 'search_div', 'subcon_dyeing_production_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div style="margin-left:3px;" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$start_date =$data[0];
	$end_date =$data[1];
	$company_id =$data[2];
	$batch_no =$data[3];
	$search_type =$data[4];
 	
	if($search_type==1)
	{
		if ($batch_no!='') $batch_cond=" and c.batch_no='$batch_no'"; else $batch_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($batch_no!='') $batch_cond=" and c.batch_no like '%$batch_no%'"; else $batch_cond="";
	}
	else if($search_type==2)
	{
		if ($batch_no!='') $batch_cond=" and c.batch_no like '$batch_no%'"; else $batch_cond="";
	}
	else if($search_type==3)
	{
		if ($batch_no!='') $batch_cond=" and c.batch_no like '%$batch_no'"; else $batch_cond="";
	}
	
	if($company_id==0) $company_cond=""; else $company_cond=" and c.company_id=$company_id";
		
	if($db_type==2)
	{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and c.batch_date between '".change_date_format($start_date, "mm-dd-yyyy", "-",1)."' and '".change_date_format($end_date, "mm-dd-yyyy", "-",1)."'"; else $batch_date_con ="";
		
		//$sql_po=sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
		$sql_po=sql_select("select b.mst_id, a.job_no_mst,a.order_no as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b,pro_batch_create_mst c where c.id=b.mst_id and a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 $company_cond $batch_date_con $batch_cond ");
		//echo "select b.mst_id, a.job_no_mst,listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst";die;
	}
	if($db_type==0)
	{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and c.batch_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $batch_date_con ="";
		
		$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat( distinct a.order_no )  as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
	}
	
	$po_num=array();
	
	foreach($sql_po as $row_po_no)
	{
		$po_num[$row_po_no[csf('mst_id')]]['po_no'].=$row_po_no[csf('po_no')].',';
		$po_num[$row_po_no[csf('mst_id')]]['job_no_mst']=$row_po_no[csf('job_no_mst')];
	} 	//and company_id=$company_id
	$sql = "select c.id, c.batch_no, c.batch_date, c.batch_weight, c.booking_no, c.extention_no, c.color_id, c.batch_against, c.re_dyeing_from from pro_batch_create_mst c where c.status_active=1 and c.is_deleted=0 and c.entry_form=36 $company_cond $batch_date_con $batch_cond order by c.id desc"; 
	//echo $sql;//die; 
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Batch No</th>
                <th width="80">Extention No</th>
                <th width="80">Batch Date</th>
                <th width="90">Batch Qnty</th>
                <th width="115">Job No</th>
                <th width="80">Color</th>
                <th>Po No</th>
            </thead>
        </table>
        <div style="width:770px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_no='';
					//echo $selectResult['re_dyeing_from'];die;
					if($selectResult[csf('re_dyeing_from')]==0)
					{	
						$poNo=rtrim($po_num[$selectResult[csf('id')]]['po_no'],',');
						$po_no=implode(",",array_unique(explode(",",$poNo)));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>)"> 
							<td width="40" align="center"><? echo $i; ?></td>	
							<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                            <td width="80"><p><? echo $selectResult[csf('extention_no')]; ?></p></td>
							<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
							<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
                            <td width="115"><p><? echo $po_num[$selectResult[csf('id')]]['job_no_mst']; ?></p></td>
							<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td><? echo $po_no; ?></td>	
						</tr>
						<?
						$i++;
					}
					else
					{ 
						 $sql_re= "select id, batch_no, batch_date, batch_weight, booking_no, MAX(extention_no) as extention_no, color_id, batch_against, re_dyeing_from from pro_batch_create_mst where entry_form=36 and status_active=1 and is_deleted=0 and id='".$selectResult[csf('re_dyeing_from')]."' group by id, batch_no, batch_date, batch_weight, booking_no,color_id, batch_against, re_dyeing_from  ";
						$dataArray=sql_select( $sql_re );
							
						foreach($dataArray as $row)
						{
							if($row[csf('re_dyeing_from')]==0)
							{
								$poNo=rtrim($po_num[$selectResult[csf('id')]]['po_no'],',');
								$po_no=implode(",",array_unique(explode(",",$poNo)));
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>)"> 
									<td width="40" align="center"><? echo $i; ?></td>	
									<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
									<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
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
if($action=="create_batch_search_list_view_old")
{
	$data = explode("_",$data);
	$start_date =$data[0];
	$end_date =$data[1];
	$company_id =$data[2];
	$batch_no =$data[3];
	$search_type =$data[4];
 	
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
	
	if($company_id==0) $company_cond=""; else $company_cond=" and company_id=$company_id";
		
	if($db_type==2)
	{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and batch_date between '".change_date_format($start_date, "mm-dd-yyyy", "-",1)."' and '".change_date_format($end_date, "mm-dd-yyyy", "-",1)."'"; else $batch_date_con ="";
		
		$sql_po=sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
	}
	if($db_type==0)
	{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and batch_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $batch_date_con ="";
		
		$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat( distinct a.order_no )  as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
	}
	
	$po_num=array();
	
	foreach($sql_po as $row_po_no)
	{
		$po_num[$row_po_no[csf('mst_id')]]['po_no']=$row_po_no[csf('po_no')];
		$po_num[$row_po_no[csf('mst_id')]]['job_no_mst']=$row_po_no[csf('job_no_mst')];
	} 	//and company_id=$company_id
	$sql = "select id, batch_no, batch_date, batch_weight, booking_no, extention_no, color_id, batch_against, re_dyeing_from from pro_batch_create_mst where status_active=1 and is_deleted=0 and entry_form=36 $company_cond $batch_date_con $batch_cond order by id desc"; 
	//echo $sql;//die; 
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Batch No</th>
                <th width="80">Extention No</th>
                <th width="80">Batch Date</th>
                <th width="90">Batch Qnty</th>
                <th width="115">Job No</th>
                <th width="80">Color</th>
                <th>Po No</th>
            </thead>
        </table>
        <div style="width:770px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_no='';
					//echo $selectResult['re_dyeing_from'];die;
					if($selectResult[csf('re_dyeing_from')]==0)
					{	
						$po_no=implode(",",array_unique(explode(",",$po_num[$selectResult[csf('id')]]['po_no'])));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>)"> 
							<td width="40" align="center"><? echo $i; ?></td>	
							<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                            <td width="80"><p><? echo $selectResult[csf('extention_no')]; ?></p></td>
							<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
							<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
                            <td width="115"><p><? echo $po_num[$selectResult[csf('id')]]['job_no_mst']; ?></p></td>
							<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td><? echo $po_no; ?></td>	
						</tr>
						<?
						$i++;
					}
					else
					{ 
						 $sql_re= "select id, batch_no, batch_date, batch_weight, booking_no, MAX(extention_no) as extention_no, color_id, batch_against, re_dyeing_from from pro_batch_create_mst where entry_form=36 and status_active=1 and is_deleted=0 and id='".$selectResult[csf('re_dyeing_from')]."' group by id, batch_no, batch_date, batch_weight, booking_no,color_id, batch_against, re_dyeing_from  ";
						$dataArray=sql_select( $sql_re );
							
						foreach($dataArray as $row)
						{
							//if($row[csf('re_dyeing_from')]>0)
							//{
								$po_no=implode(",",array_unique(explode(",",$po_num[$selectResult[csf('id')]]['po_no'])));
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>)"> 
									<td width="40" align="center"><? echo $i; ?></td>	
									<td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
									<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
									<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
									<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
								
                                    <td width="115"><p><? echo $po_num[$selectResult[csf('id')]]['job_no_mst']; ?></p></td>
									<td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
									<td><? echo $po_no; ?></td>	
								</tr>
								<?
								$i++;
							//}
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

if($action=='populate_data_from_batch')
{ 
	$ex_data=explode('_',$data);
	$load_unload=$ex_data[0];
	$batch_id=$ex_data[1]; 
	$batch_no=$ex_data[2]; 
	$company=$ex_data[3]; 
	//$company_id=return_field_value("company_id","pro_batch_create_mst","id ='$batch_id' and is_deleted=0 and status_active=1");
	
	$batch_data =sql_select("select company_id,working_company_id,entry_form,double_dyeing,machine_no,floor_id,total_trims_weight from pro_batch_create_mst where id ='$batch_id' and is_deleted=0 and status_active=1");
	foreach ($batch_data as $row)
	{
		$company_id = $row[csf('company_id')];
		$working_company_id = $row[csf('working_company_id')];
		$entry_form_id= $row[csf('entry_form')];
		$double_dyeing = $row[csf('double_dyeing')];
		$total_trims_weight = $row[csf('total_trims_weight')];
		$dyeing_machine = $row[csf('machine_no')];
		$floor_id = $row[csf('floor_id')];
	}
	// echo $total_trims_weight.'TTTTTTTTTTTTTT';
	//$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and variable_list=3 and is_deleted=0 and status_active=1");

	$ltb_btb=array(1=>'BTB',2=>'LTB');
	if($db_type==0) $select_field1="order by a.id"; 
	else if($db_type==2) $select_field1=" group by a.id, a.batch_no, a.batch_weight, a.color_id, a.company_id, a.process_id order by a.id";
	if($db_type==0) $select_list="group_concat(distinct(b.po_id)) as po_id"; 
	else if($db_type==2) $select_list=" listagg(b.po_id,',') within group (order by b.po_id) as po_id";
	//$booking_id=implode(",",array_unique(explode(",",$booking_id)));
	if($batch_no!='')
	{ 
		$data_array=sql_select("select a.id as id, a.batch_no, a.company_id, a.batch_weight, Max(a.extention_no) as extention_no, a.process_id as process_id_batch, a.color_id,  sum(b.batch_qnty) as batch_qnty,  $select_list from pro_batch_create_mst a, pro_batch_create_dtls b where 
		a.id='$batch_id' and a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $select_field1");
	}
	else
	{
		$data_array=sql_select("select a.id as id, a.batch_no, a.company_id, a.batch_weight, Max(a.extention_no) as extention_no, a.process_id as process_id_batch, a.color_id, sum(b.batch_qnty) as batch_qnty, $select_list  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id='$batch_id' and a.entry_form=36 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $select_field1");
	}
	if($db_type==0) $select_f_group=""; 
	else if($db_type==2) $select_f_group="group by a.job_no_mst, b.buyer_name";
	if($db_type==0) $select_listagg="group_concat(distinct(a.po_number)) as po_no"; 
	else if($db_type==2) $select_listagg="listagg(cast(a.po_number as varchar(500)),',') within group (order by a.po_number) as po_no";
	
	if($db_type==0) $select_listagg_subcon="group_concat(distinct(a.order_no)) as po_no"; 
	else if($db_type==2) $select_listagg_subcon="listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no";

	foreach ($data_array as $row)
	{ 
		$pro_id=implode(",",array_unique(explode(",",$row[csf('po_id')])));
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		
		 $inhouse=1;
		echo "document.getElementById('cbo_service_source').value 			= '" . $inhouse . "';\n";
		echo "load_drop_down( 'requires/subcon_dyeing_production_controller', " . $inhouse . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "document.getElementById('cbo_service_company').value 				= '".$company_id."';\n";
	  //  echo "load_drop_down( 'requires/subcon_dyeing_production_controller', '".$row[csf("company_id")]."', 'load_drop_floor', 'floor_td' );\n";
		echo "$('#cbo_company_id').attr('disabled',true);\n";
		echo "document.getElementById('txt_batch_no').value 			= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('hidden_batch_id').value 			= '".$row[csf("id")]."';\n";	
		echo "document.getElementById('txt_batch_ID').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_color').value 				= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_ext_id').value 				= '".$row[csf("extention_no")]."';\n";
		echo "document.getElementById('txt_trim_wgt').value 			= '" . $total_trims_weight . "';\n";
		
		/*if($roll_maintained==1)
		{
		echo "$('#txt_issue_chalan').attr('disabled',false);\n";	
		}*/
			
		// $result_job=sql_select("select $select_listagg_subcon, b.subcon_job as job_no_mst, b.party_id as buyer_name from  subcon_ord_dtls a, 
		//  subcon_ord_mst b where a.job_no_mst=b.subcon_job and a.id in(".$pro_id.") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 
		// and a.is_deleted=0 group by b.subcon_job, b.party_id");
		$result_job=sql_select("select a.a.order_no as po_no, b.subcon_job as job_no_mst, b.party_id as buyer_name from  subcon_ord_dtls a, 
		 subcon_ord_mst b where a.job_no_mst=b.subcon_job and a.id in(".$pro_id.") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 
		and a.is_deleted=0 ");
		foreach($result_job as $row)
		{
			$po_noArr[$row[csf("po_no")]]=$row[csf("po_no")];
			$job_no_mst=$row[csf("job_no_mst")];
			$buyer_name=$row[csf("buyer_name")];
		}
		
		
		$process_name_batch='';
		$process_id_array=explode(",",$row[csf("process_id_batch")]);
		foreach($process_id_array as $val)
		{
			if($process_name_batch=="") $process_name_batch=$conversion_cost_head_array[$val]; else $process_name_batch.=",".$conversion_cost_head_array[$val];
		}
		echo "document.getElementById('txt_process_id').value 			= '31';\n";
		//echo "document.getElementById('txt_process_name').value 			= '".$process_name_batch."';\n";
		
		$pro_id2=implode(",",$po_noArr);
		echo "document.getElementById('txt_buyer').value 				= '".$buyer_arr[$buyer_name]."';\n";
		echo "document.getElementById('txt_job_no').value 				= '".$job_no_mst."';\n";
		echo "document.getElementById('txt_order_no').value 			= '".$pro_id2."';\n";
		
		echo "document.getElementById('cbo_floor').value 			= '" . $floor_id . "';\n";
		if($floor_id>0)
		{
		echo "load_drop_down( 'requires/subcon_dyeing_production_controller', $company_id+'**'+$floor_id, 'load_drop_machine', 'machine_td' );\n";
		}
		if($dyeing_machine>0)
		{
		echo "document.getElementById('cbo_machine_name').value 			= '" . $dyeing_machine . "';\n";
		}
		
		$sql_batch_d=sql_select("select id, service_source, service_company, received_chalan, system_no,issue_chalan, issue_challan_mst_id,company_id,batch_id,batch_no,process_end_date,end_hours,end_minutes,machine_id,floor_id,process_id,ltb_btb_id,remarks from 
		pro_fab_subprocess where batch_id='$batch_id' and entry_form=38 and load_unload_id=1 and status_active=1 and is_deleted=0");
		
		foreach($sql_batch_d as $dyeing_d)
		{//$minute=str_pad($r_batch[csf("end_minutes")],2,'0',STR_PAD_LEFT);
			echo "document.getElementById('txt_dying_started').value = '".change_date_format($dyeing_d[csf("process_end_date")])."';\n";
			echo "document.getElementById('txt_dying_end_load').value = '".str_pad($dyeing_d[csf("end_hours")],2,'0',STR_PAD_LEFT).':'.str_pad($dyeing_d[csf("end_minutes")],2,'0',STR_PAD_LEFT)."';\n";
			echo "$('#txt_issue_chalan').val('".$dyeing_d[csf("issue_chalan")]."');\n";
			echo "$('#cbo_service_source').val(".$dyeing_d[csf("service_source")].");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '".$dyeing_d[csf('received_chalan')]."';\n";
			echo "document.getElementById('txt_system_no').value	= '" . $dyeing_d[csf('system_no')] . "';\n";
			echo "load_drop_down( 'requires/subcon_dyeing_production_controller', ".$dyeing_d[csf("service_source")]."+'**'+".$dyeing_d[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(".$dyeing_d[csf("service_company")].");\n";
			echo "document.getElementById('txt_ltb_btb').value	= '".$ltb_btb[$dyeing_d[csf("ltb_btb_id")]]."';\n";
		}
		//exit();
	}
	
	if($db_type==0) $select_group_row1=" order by id desc limit 0,1"; 
	else if($db_type==2) $select_group_row1=" and  rownum>=1 order by id desc";//order by id desc limit 0,1
	if($load_unload==1)
	{ 
		$sql_batch=sql_select("select id, batch_no, company_id, batch_id, service_source, service_company, received_chalan, issue_chalan, issue_challan_mst_id, process_end_date, load_unload_id, end_hours, end_minutes, machine_id, floor_id, process_id, ltb_btb_id, water_flow_meter, result, remarks, multi_batch_load_id from pro_fab_subprocess where batch_id='$batch_id' and entry_form=38 and load_unload_id in(1) and status_active=1 and is_deleted=0 ");
	}
	else if($load_unload==2)
	{ 
		$sql_batch=sql_select("select id, batch_no, company_id, service_source, service_company, received_chalan, issue_chalan, issue_challan_mst_id, batch_id, process_end_date, load_unload_id, end_hours, end_minutes, machine_id, floor_id, process_id, ltb_btb_id, water_flow_meter, result, remarks, shift_name, fabric_type, production_date from pro_fab_subprocess where batch_id='$batch_id' and entry_form=38 and load_unload_id in(2,1) and status_active=1 and is_deleted=0 $select_group_row1");
	}
	
	foreach($sql_batch as $r_batch)
	{
		if($load_unload==1) //Load
		{
			if($r_batch[csf('load_unload_id')]==1)
			{
				echo "document.getElementById('txt_update_id').value 				= '".$r_batch[csf("id")]."';\n";	
			}
			else
			{
				echo "document.getElementById('txt_update_id').value 				= '';\n";
			}
			
			$process_name='';
			$process_id_array=explode(",",$r_batch[csf("process_id")]);
			foreach($process_id_array as $val)
			{
				if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}	
		
			echo "document.getElementById('txt_process_start_date').value 		= '".change_date_format($r_batch[csf("process_end_date")])."';\n";
			//echo "document.getElementById('cbo_sub_process').value 				= '".$r_batch[csf("process_id")]."';\n";
			echo "document.getElementById('txt_process_id').value 				= '".$r_batch[csf("process_id")]."';\n";
			//echo "document.getElementById('txt_process_name').value 			= '".$process_name."';\n";
			//echo "set_multiselect('cbo_sub_process','0','1','".$r_batch[csf('process_id')]."','0');\n";
			echo "document.getElementById('cbo_ltb_btb').value	= '".$r_batch[csf("ltb_btb_id")]."';\n";
			echo "document.getElementById('txt_water_flow').value	= '".$r_batch[csf("water_flow_meter")]."';\n";
			echo "document.getElementById('cbo_yesno').value	= '".$r_batch[csf("multi_batch_load_id")]."';\n";
			$minute=str_pad($r_batch[csf("end_minutes")],2,'0',STR_PAD_LEFT);
			$hour=str_pad($r_batch[csf("end_hours")],2,'0',STR_PAD_LEFT);
			echo "document.getElementById('txt_start_minutes').value	= '".$minute."';\n";
			echo "document.getElementById('txt_start_hours').value	= '".$hour."';\n";
			echo "$('#txt_issue_chalan').val('".$r_batch[csf("issue_chalan")]."');\n";
			echo "$('#cbo_service_source').val(".$r_batch[csf("service_source")].");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '".$r_batch[csf('received_chalan')]."';\n";
			echo "load_drop_down( 'requires/subcon_dyeing_production_controller', ".$r_batch[csf("service_source")]."+'**'+".$r_batch[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(".$r_batch[csf("service_company")].");\n";
			echo "load_drop_down( 'requires/subcon_dyeing_production_controller', document.getElementById('cbo_service_company').value, 'load_drop_floor', 'floor_td' );\n";
			
			echo "load_drop_down( 'requires/subcon_dyeing_production_controller', document.getElementById('cbo_company_id').value+'**'+".$r_batch[csf("floor_id")].", 'load_drop_machine', 'machine_td' );\n";
			echo "document.getElementById('cbo_floor').value = '".$r_batch[csf("floor_id")]."';\n";
			echo "document.getElementById('cbo_machine_name').value = '".$r_batch[csf("machine_id")]."';\n";
			echo "document.getElementById('txt_remarks').value	= '".$r_batch[csf("remarks")]."';\n";
			if($r_batch[csf("id")]!=0)
			{
				echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',1);\n"; 	
			}
			else
			{
				echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',0);\n"; 		
			}
		}
		else if($load_unload==2) //Unload
		{ 
			if($r_batch[csf("load_unload_id")]==2)
			{
				echo "document.getElementById('txt_update_id').value 		= '".$r_batch[csf("id")]."';\n";
				echo "document.getElementById('txt_process_end_date').value = '".($r_batch[csf("load_unload_id")] == 1 ? "" : change_date_format($r_batch[csf("process_end_date")]))."';\n";
				echo "document.getElementById('txt_process_date').value = '".($r_batch[csf("load_unload_id")] == 1 ? "" : change_date_format($r_batch[csf("production_date")]))."';\n";
				echo "document.getElementById('cbo_shift_name').value	= '".$r_batch[csf("shift_name")]."';\n";
				echo "document.getElementById('cbo_fabric_type').value	= '".$r_batch[csf("fabric_type")]."';\n";
				echo "document.getElementById('txt_water_flow').value	= '".($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("water_flow_meter")])."';\n";
				echo "document.getElementById('cbo_ltb_btb').value	= '".$r_batch[csf("ltb_btb_id")]."';\n";
			}
			//$process_name=$r_batch[csf('process_id')];
			//echo "document.getElementById('cbo_sub_process').value 		= '".$r_batch[csf("process_id")]."';\n";
			//echo "set_multiselect('cbo_sub_process','0','1','".$r_batch[csf("process_id")]."','0');\n";
			$process_name='';
			$process_id_array=explode(",",$r_batch[csf("process_id")]);
			foreach($process_id_array as $val)
			{
				if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}	
			//echo "load_drop_down( 'requires/dyeing_production_controller', '".$r_batch[csf("process_id")]."', 'load_drop_sub_process', 'sub_process_td' );\n";
			// echo "set_multiselect('cbo_sub_process','0','0','0','0');\n";
			//echo "set_multiselect('cbo_sub_process','0','1','".$r_batch[csf('process_id')]."','0');\n";
			//echo "document.getElementById('txt_end_minutes').value	= '".($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("end_minutes")])."';\n";
			//echo "document.getElementById('txt_end_hours').value	= '".($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("end_hours")])."';\n";
			echo "document.getElementById('txt_process_id').value 				= '".$r_batch[csf("process_id")]."';\n";
			//echo "document.getElementById('txt_process_name').value 				= '".$process_name."';\n";
			if($r_batch[csf("load_unload_id")]==2)
			{
				$minute=str_pad($r_batch[csf("end_minutes")],2,'0',STR_PAD_LEFT);
				$hour=str_pad($r_batch[csf("end_hours")],2,'0',STR_PAD_LEFT);
				echo "document.getElementById('txt_end_minutes').value	= '".$minute."';\n";
				echo "document.getElementById('txt_end_hours').value	= '".$hour."';\n";
			}
			echo "$('#txt_issue_chalan').val('".$r_batch[csf("issue_chalan")]."');\n";
			echo "$('#cbo_service_source').val(".$r_batch[csf("service_source")].");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '".$r_batch[csf('received_chalan')]."';\n";
			echo "load_drop_down( 'requires/subcon_dyeing_production_controller', ".$r_batch[csf("service_source")]."+'**'+".$r_batch[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(".$r_batch[csf("service_company")].");\n";
			echo "document.getElementById('cbo_floor').value = '".$r_batch[csf("floor_id")]."';\n";
			echo "load_drop_down( 'requires/subcon_dyeing_production_controller', document.getElementById('cbo_company_id').value+'**'+".$r_batch[csf("floor_id")].", 'load_drop_machine', 'machine_td' );\n";
			echo "document.getElementById('cbo_machine_name').value = '".$r_batch[csf("machine_id")]."';\n";
			//echo "document.getElementById('cbo_ltb_btb').value	= '".$r_batch[csf("ltb_btb_id")]."';\n";
			if($r_batch[csf("load_unload_id")]==2)
			{
				echo "document.getElementById('cbo_result_name').value	= '".$r_batch[csf("result")]."';\n";
				echo "document.getElementById('txt_remarks').value	= '".($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("remarks")])."';\n";
			}
			//echo "document.getElementById('txt_remarks').value	= '".$r_batch[csf("remarks")]."';\n";
			echo "$('#cbo_machine_name').attr('disabled',true);\n";
			echo "$('#cbo_floor').attr('disabled',true);\n";
			if($r_batch[csf("load_unload_id")]==2)
			{
				echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',1);\n"; 	
			}
			else
			{
				echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_pro_fab_subprocess',0);\n";	
			}
		}
	}
	exit();	
}

if($action=='show_fabric_desc_listview')
{
	$ex_data=explode('_',$data);
	$batch_id=$ex_data[0];
	//$roll_maintained=$ex_data[1];
	$company_id=return_field_value("company_id","pro_batch_create_mst","id ='$batch_id' and is_deleted=0 and status_active=1");
	//$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and variable_list=3 and is_deleted=0 and status_active=1");
	
	/*$fabric_roll_arr=array();
	$prollData=sql_select("select id,roll_no,barcode_no from pro_roll_details where  status_active=1 and is_deleted=0");
	foreach($prollData as $row)
	{
		$fabric_roll_arr[$row[csf('id')]]['roll']=$row[csf('roll_no')];
	}
	$fabric_desc_arr=array();
	$prodData=sql_select("select id, item_description, lot,gsm,yarn_count_id,brand, dia_width from product_details_master where item_category_id=13");
	foreach($prodData as $row)
	{
		$fabric_desc_arr[$row[csf('id')]]['desc']=$row[csf('item_description')];
		$fabric_desc_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$fabric_desc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}
	*/
	/*$yarn_lot_arr=array();
	if($db_type==0)
	{
		$yarn_lot_data=sql_select("select  b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, group_concat(distinct(a.brand_id)) as brand_id,group_concat( distinct a.yarn_count,'**') AS yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
	}
	else if($db_type==2)
	{
		$yarn_lot_data=sql_select("select  b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, listagg(cast(a.yarn_count as varchar2(4000)),'**') within group (order by a.yarn_count) AS yarn_count, LISTAGG(a.brand_id,',') WITHIN GROUP ( ORDER BY a.brand_id) as brand_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
	}
	foreach($yarn_lot_data as $rows)
	{
		$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
		$brand_id=explode(",",$rows[csf('brand_id')]);
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot']=implode(", ",array_unique($yarn_lot));
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count']=$rows[csf('yarn_count')];
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id']=implode(", ",array_unique($brand_id));
	}*/
		
	if($db_type==0) $select_group=" group by b.id, b.item_description"; 
	else if($db_type==2) $select_group="group by b.id, b.item_description,b.gsm, b.width_dia_type, b.prod_id, b.po_id, b.roll_no, b.roll_id,b.fin_dia";//order by id desc limit 0,1
	$result=sql_select("select b.width_dia_type, b.prod_id, b.po_id, b.roll_no, b.roll_id,b.gsm, b.item_description, b.width_dia_type, b.fin_dia, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b, pro_batch_create_mst a where b.mst_id=$batch_id and a.id=b.mst_id and  a.entry_form=36 and b.status_active=1 and b.is_deleted=0 $select_group");
	//echo "select b.width_dia_type, b.prod_id, b.po_id, b.roll_no, b.roll_id, b.item_description, b.width_dia_type, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b, pro_batch_create_mst a where b.mst_id=$batch_id and a.id=b.mst_id and  a.entry_form=36 and b.status_active=1 and b.is_deleted=0 $select_group";
	$i=1;
	$b_qty=0;
	foreach($result as $row)
	{
		$desc=explode(",",$row[csf('item_description')]);
		//print_r($desc);
		$cons_comps=$desc[0].','.$desc[1];
		$gsm=$row[csf('gsm')];
		$dia_width=$row[csf('fin_dia')];

		/*$brand=$lot=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];
		$lot=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
		$brand_id=explode(',',$brand);
		$brand_value="";
		foreach($brand_id as $val)
		{
			if($val>0)
			{
				if($brand_value=='') $brand_value=$brand_name[$val]; else $brand_value.=", ".$brand_name[$val];
			}
		}
		$y_count_id=$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'];
		$count_id=array_unique(explode("**",$y_count_id));
		//print_r( $count_id).'aziz';
		//array_unique(explode(',',$y_count));
		$yarn_count_value='';
		foreach($count_id as $val)
		{
			if($val>0)
			{
				if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
			}
		}*/
		/*if($roll_maintained==1) 
		{
			$roll_no=$fabric_roll_arr[$row[csf('roll_id')]]['roll'];
		//echo $row[csf('roll_id')];die;
		}
		else
		{
			$roll_no=$row[csf('roll_no')];	
		}*/
		$roll_no=$row[csf('roll_no')];		
	?>
    	<tr class="general" id="row_<? echo $i; ?>">
            <td><input type="text" name="txt_cons_comp_<? echo $i; ?>" id="txt_cons_comp_<? echo $i; ?>" class="text_boxes" style="width:170px;" value="<? echo $cons_comps; ?>" disabled/></td>
            <td><input type="text" name="txt_gsm_<? echo $i; ?>" id="txt_gsm_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $gsm; ?>" disabled/></td>
            <td><input type="text" name="txt_body_part_<? echo $i; ?>" id="txt_body_part_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; ?>" disabled/></td>
            <td><input type="text" name="txt_dia_width_<? echo $i; ?>" id="txt_dia_width_<? echo $i; ?>" class="text_boxes" style="width:70px;" value="<? echo  $fabric_typee[$row[csf('width_dia_type')]]; ?>" disabled/></td>
            <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $roll_no;?>" disabled/></td>
            <td><input type="text" name="txt_batch_qnty_<? echo $i; ?>" id="txt_batch_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($row[csf('batch_qnty')]); ?>" disabled/></td>
            <td><input type="text" name="txtlot_<? echo $i; ?>" id="txtlot_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $lot;?>" readonly disabled /></td>
			<td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $yarn_count_value;?>" disabled /></td>
			<td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $brand_value;?>" disabled /> </td>
        </tr>
    <?
	$b_qty+= $row[csf('batch_qnty')];
		$i++;
	}
	?>
	<tr>
        <td colspan="5" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
        <td align="right"><? echo number_format($b_qty,2); ?> </td>
	</tr>
	<?
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
		$field_array="";
		$data_array="";
		$id=return_next_id( "id", "pro_fab_subprocess", 1 ) ;
		//echo $cbo_load_unload;die;multi_batch_load_id
		if($cbo_load_unload=="'1'")
		{
			if (str_replace("'", '', $cbo_load_unload) == 1) {
		
			$sql_load="select id, batch_id from pro_fab_subprocess where  company_id=".$cbo_company_id." and  batch_id=".$txt_batch_ID." and load_unload_id=1 and entry_form=38 and is_deleted=0 and status_active=1";
			$load_data_array=sql_select($sql_load);
			if(count($load_data_array)>0)
			{
				echo "13**".'Duplicate load Found';disconnect($con);die;
			}
		}
		
			$txt_system_no = str_replace("'", "", $txt_system_no);
			if ($txt_system_no == "") $system_no = $id + 1; else $system_no = $txt_system_no;
			$field_array="id, company_id,system_no, service_source, service_company, received_chalan, issue_chalan, issue_challan_mst_id, batch_no, batch_id, batch_ext_no, process_id, ltb_btb_id, water_flow_meter, process_end_date, end_hours, end_minutes, machine_id, floor_id, load_unload_id, entry_form, remarks, multi_batch_load_id, inserted_by, insert_date";			
			$data_array="(".$id.",".$cbo_company_id.",".$system_no.",".$cbo_service_source.",".$cbo_service_company.",".$txt_recevied_chalan.",".$txt_issue_chalan.",".$txt_issue_mst_id.",".$txt_batch_no.",".$txt_batch_ID.",".$txt_ext_id.",".$txt_process_id.",".$cbo_ltb_btb.",".$txt_water_flow.",".$txt_process_start_date.",".$txt_start_hours.",".$txt_start_minutes.",".$cbo_machine_name.",".$cbo_floor.",".$cbo_load_unload.",38,".$txt_remarks.",".$cbo_yesno.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		if($cbo_load_unload=="'2'")
		{
			$sql_unload="select id, batch_id from pro_fab_subprocess where  company_id=".$cbo_company_id." and  batch_id=".$txt_batch_ID." and load_unload_id=2 and entry_form=38 and is_deleted=0 and status_active=1";
			$unload_data_array=sql_select($sql_unload);
			if(count($unload_data_array)>0)
			{
				echo "11**".'Duplicate Unload Found';
				disconnect($con);
				die;
			}
			$sql_data = "select id, batch_id from pro_fab_subprocess where  company_id=" . $cbo_company_id . " and  batch_id=" . $txt_batch_ID . " and load_unload_id=1 and entry_form=38 and is_deleted=0 and status_active=1";
				$data_array = sql_select($sql_data);
				if (count($data_array) > 0) {
					//secho "1**" . $data_array[0][csf('batch_id')];
				} else {
					echo "100**" . 'Without Load  Unload Not Allow';
					disconnect($con);
					die;
				}
			
			$system_no = str_replace("'", "", $txt_system_no);
			$result_id = str_replace("'", "", $cbo_result_name);
			if($result_id==4) //incomplete
			{
				$field_arr=",incomplete_result";
				$field_data_arr=",".$result_id;
			}
			elseif($result_id==2) //Redying Shade Match
			{
				$field_arr=",redyeing_needed";
				$field_data_arr=",".$result_id;
			}
			elseif($result_id==1) //Shade Match
			{
				$field_arr=",shade_matched";
				$field_data_arr=",".$result_id;
			}
			else
			{
				$field_arr="";
				$field_data_arr="";
			}
			
			$field_array="id, company_id,system_no,service_source, service_company, received_chalan, issue_chalan, issue_challan_mst_id, batch_no, batch_id, batch_ext_no, process_id, ltb_btb_id, water_flow_meter, process_end_date, end_hours, end_minutes, machine_id, floor_id, load_unload_id, result, entry_form, remarks, shift_name, fabric_type, production_date, inserted_by, insert_date $field_arr";			
			$data_array="(".$id.",".$cbo_company_id.",".$system_no.",".$cbo_service_source.",".$cbo_service_company.",".$txt_recevied_chalan.",".$txt_issue_chalan.",".$txt_issue_mst_id.",".$txt_batch_no.",".$hidden_batch_id.",".$txt_ext_id.",".$txt_process_id.",".$cbo_ltb_btb.",".$txt_water_flow.",".$txt_process_end_date.",".$txt_end_hours.",".$txt_end_minutes.",".$cbo_machine_name.",".$cbo_floor.",".$cbo_load_unload.",".$cbo_result_name.",38,".$txt_remarks.",".$cbo_shift_name.",".$cbo_fabric_type.",".$txt_process_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' ".$field_data_arr.")";
		//print_r($data_array);  
		}
		$rID=sql_insert("pro_fab_subprocess",$field_array,$data_array,0);
		//echo "insert into pro_fab_subprocess (".$field_array.") values ".$data_array;die;
		//check_table_status( $_SESSION['menu_id'],0);	
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**" . $id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**" . $id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id;
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
		$update_id=str_replace("'","",$txt_update_id);
		$system_no = str_replace("'", "", $txt_system_no);
		$field_array="";
		$data_array="";
		$id=return_next_id( "id", "pro_fab_subprocess", 1 ) ;
		//echo $cbo_load_unload;die;
		if($cbo_load_unload=="'1'")
		{
			$field_array_update="company_id*service_source*service_company*received_chalan*issue_chalan*issue_challan_mst_id*batch_no*batch_id*batch_ext_no*process_id*ltb_btb_id*water_flow_meter*process_end_date*end_hours*end_minutes*machine_id*floor_id*load_unload_id*entry_form*remarks*multi_batch_load_id*updated_by*update_date";
			$data_array_update="".$cbo_company_id."*".$cbo_service_source."*".$cbo_service_company."*".$txt_recevied_chalan."*".$txt_issue_chalan."*".$txt_issue_mst_id."*".$txt_batch_no."*".$txt_batch_ID."*".$txt_ext_id."*".$txt_process_id."*".$cbo_ltb_btb."*".$txt_water_flow."*".$txt_process_start_date."*".$txt_start_hours."*".$txt_start_minutes."*".$cbo_machine_name."*".$cbo_floor."*".$cbo_load_unload."*38*".$txt_remarks."*".$cbo_yesno."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		if($cbo_load_unload=="'2'")
		{
			$result_id = str_replace("'", "", $cbo_result_name);
			if($result_id==4) //incomplete
			{
				$field_arr="*incomplete_result";
				$field_data_arr="*".$result_id;
			}
			elseif($result_id==2) //Redying Shade Match
			{
				$field_arr="*redyeing_needed";
				$field_data_arr="*".$result_id;
			}
			elseif($result_id==1) // Shade Match
			{
				$field_arr="*shade_matched";
				$field_data_arr="*".$result_id;
			}
			else
			{
				$field_arr="";
				$field_data_arr="";
			}
			$field_array_update="company_id*service_source*service_company*received_chalan*issue_chalan*issue_challan_mst_id*batch_no*batch_id*batch_ext_no*process_id*ltb_btb_id*water_flow_meter*process_end_date*end_hours*end_minutes*machine_id*floor_id*load_unload_id*result*entry_form*remarks*shift_name*fabric_type*production_date*updated_by*update_date $field_arr";
			$data_array_update="".$cbo_company_id."*".$cbo_service_source."*".$cbo_service_company."*".$txt_recevied_chalan."*".$txt_issue_chalan."*".$txt_issue_mst_id."*".$txt_batch_no."*".$hidden_batch_id."*".$txt_ext_id."*".$txt_process_id."*".$cbo_ltb_btb."*".$txt_water_flow."*".$txt_process_end_date."*".$txt_end_hours."*".$txt_end_minutes."*".$cbo_machine_name."*".$cbo_floor."*".$cbo_load_unload."*".$cbo_result_name."*38*".$txt_remarks."*".$cbo_shift_name."*".$cbo_fabric_type."*".$txt_process_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."' ".$field_data_arr."";
		}
		$rID=sql_update("pro_fab_subprocess",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo "insert into pro_fab_subprocess values $field_array_update,$field_array_update,'id',$update_id,0)";die;
		//check_table_status( $_SESSION['menu_id'],0);	
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**" . $update_id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);

			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "1**" . $update_id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);

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
	else if ($operation==2)   // Delete Here
	{
	   echo "2**".$update_id;
	   die;
	}
}

if($action=="check_batch_no")
{
	$data=explode("**",$data);
	$sql="select id, batch_no,company_id from pro_batch_create_mst where batch_no='".trim($data[1])."' and entry_form=36 and is_deleted=0 and status_active=1 order by id desc";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('company_id')];
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
	$batch_id=(int) $data[1];
	//$sql="select id, batch_no,company_id from pro_batch_create_mst where id='".$batch_id."' and entry_form=36 and is_deleted=0 and status_active=1 order by id desc";
	$sql="select id, batch_no,company_id from pro_batch_create_mst where batch_no='".$batch_id."' and entry_form=36 and is_deleted=0 and status_active=1 order by id desc";
	$data_array=sql_select($sql,1);
	echo $data_array[0][csf('batch_no')];
	exit();	
}

if($action=="check_batch_no_load")
{
	$data=explode("**",$data);
	$sql="select id, batch_id from pro_fab_subprocess where  company_id='".trim($data[0])."' and  batch_id='".trim($data[2])."' and load_unload_id=1 and entry_form=38 and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('batch_id')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}

if($action=="check_batch_no_for_machine")
{
	$data=explode("**",$data);
	$sql="select  batch_no as batch_no from pro_fab_subprocess where company_id='".trim($data[0])."' and machine_id='$data[3]' and load_unload_id=1 and entry_form=38 and is_deleted=0 and status_active=1 and batch_id not in(select batch_id from pro_fab_subprocess where company_id='".trim($data[0])."' and machine_id='$data[3]' and load_unload_id=2 and entry_form=38 and is_deleted=0 and status_active=1 ) ";
	$data_array=sql_select($sql,1);
	//echo count($data_array);die;
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('batch_no')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}

if($action=="check_for_shade_matched")
{
	$data=explode("**",$data);
	//listagg(batch_id,',') within group (order by batch_id) as batch_id
	$sql_unload="select  batch_no from pro_fab_subprocess where  company_id='".trim($data[0])."'   batch_id=".trim($data[2])." and load_unload_id=2 and entry_form=38 and is_deleted=0 and result=1 and status_active=1";
	$data_array=sql_select($sql_unload,1);
	//echo count($data_array);die;
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('batch_no')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}

if ($action=="on_change_data")
{	
	extract($_REQUEST);
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	$company_id = $explode_data[1];
	if($data=="1") // Loading
	{
		?>
		<div onLoad="set_hotkey();">
		<fieldset>
            <table cellpadding="0" cellspacing="2" width="100%" id="main_tbl">
                <tr> 
                    <td width="" id="batch_no_th">Batch No.</td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan" onDblClick="openmypage_batchnum();" onChange="check_batch();"   />
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" readonly />
                    </td>
                </tr>
                <tr>
                    <td id="company_th" width="130">Company</td>
                    <td>
						<?
                        	echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "roll_maintain();","","","","","" );
                        ?>
                        <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:100px;" class="text_boxes"  readonly />
                        <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;" class="text_boxes" />
                    </td>
                </tr>
                <tr>
                    <td class="">Issue Challan</td>
                    <td>
                        <input type="text" name="txt_issue_chalan" id="txt_issue_chalan"  class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan"  onDblClick="openmypage_issue_challan();" onChange="check_issue_challan();"   />
                        <input type="hidden" name="txt_issue_mst_id" id="txt_issue_mst_id" style="width:100px;" class="text_boxes" readonly />
                        <input type="hidden" name="txt_roll_id" id="txt_roll_id" style="width:50px;" class="text_boxes" readonly />
                        <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;" class="text_boxes" />
                    </td>
                </tr>
                <tr>
                    <td id="service_source_caption">Service Source</td>
                    <td>
						<?
                       		echo create_drop_down( "cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/subcon_dyeing_production_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );search_populate(this.value);","","1,3" );
                    ?>
                    </td>
                </tr>
                <tr>
                    <td id="service_company_caption">Service Company</td>
                    <td id="dyeing_company_td">
						<?
                        	echo create_drop_down( "cbo_service_company", 135, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="search_by_th_up">Received Challan</td>
                    <td>
                    	<input type="text" name="txt_recevied_chalan" id="txt_recevied_chalan"  class="text_boxes" style="width:122px;"   />
                    </td>
                </tr>
                <tr>
                    <td>Process </td>
                    <td>
						<?
                        	echo create_drop_down( "txt_process_id", 135, $conversion_cost_head_array,"", 0, "", 31, "","","","","","1,2,3,4,101,120,121,122,123,124");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="ltb_ltb_caption">LTB/BTB </td>
                    <td>
						<? $ltb_btb=array(1=>'BTB',2=>'LTB');
                        	echo create_drop_down( "cbo_ltb_btb", 135, $ltb_btb,"", 1, "-- Select --", 1, "","","","","","");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="">Water Flow </td>
                    <td>
                    	<input type="text" name="txt_water_flow" class="text_boxes_numeric" id="txt_water_flow" style="width:122px;" />
                    </td>
                </tr>
                <tr>
                    <td id="process_start_date">Process Start Date</td>
                    <td>
                    	<input type="text" name="txt_process_start_date" id="txt_process_start_date" class="datepicker" style="width:122px;" value="<?=date("d-m-Y");?>"  readonly/>
                    </td>
                </tr>
                <tr>
                    <td id="hour_min_td">Process Start Time</td>
                    <td>
                        <input type="text" name="txt_start_hours" id="txt_start_hours" class="text_boxes_numeric" placeholder="Hours" style="width:50px;" onBlur="fnc_move_cursor(this.value,'txt_start_hours','txt_end_date',2,23)" value="<? echo date('H');?>"  />
                        <input type="text" name="txt_start_minutes" id="txt_start_minutes" class="text_boxes_numeric" placeholder="Minutes" style="width:50px;" onBlur="fnc_move_cursor(this.value,'txt_start_minutes','txt_end_date',2,59)" value="<? echo date('i');?>"  />
                    </td>
                </tr>
                <tr>
                    <td id="floor_caption">Floor</td>
                        <td id="floor_td">
                        <?
                        	echo create_drop_down( "cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0,"","","","",4 );
                        ?>
                    </td>
                <tr>
                <tr>
                    <td id="machine_caption">Machine Name</td>
                    <td id="machine_td">
						<?
                        	echo create_drop_down("cbo_machine_name", 135, $blank_array,"", 1, "-- Select Machine --", 0, "",0,"","","",""); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Multi Batch Loading</td>
                    <td>
						<?
                        	echo create_drop_down("cbo_yesno", 80, $yes_no,"", 1, "-- Select--", 1, "",0,"","","",""); 
                        ?>
                    </td>
                </tr>
            </table>
		</fieldset>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		</div>
	<?    				
    }
    if($data=="2") // Un-loading
	{
		?>
		<fieldset>
            <table cellpadding="0" cellspacing="2" width="100%" id="main_tbl">
                <tr> 
                    <td width="" id="batch_no_th">Batch No.</td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan"  onDblClick="openmypage_batchnum();" onChange="check_batch();" />
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" readonly />
                    </td>
                </tr>
                <tr>
                    <td id="company_th" width="130">Company</td>
                    <td>
						<?
						//load_drop_down('requires/subcon_dyeing_production_controller', this.value, 'load_drop_floor', 'floor_td' )
                        	echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "roll_maintain();" );
                        ?>
                        <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:100px;" class="text_boxes" readonly />
                        <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;" class="text_boxes" />
                    </td>
                </tr>
                <tr>
                    <td class="">Issue Challan</td>
                    <td>
                        <input type="text" name="txt_issue_chalan" id="txt_issue_chalan"  class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan"  onDblClick="openmypage_issue_challan();" onChange="check_issue_challan();"   />
                        <input type="hidden" name="txt_issue_mst_id" id="txt_issue_mst_id" style="width:100px;" class="text_boxes" readonly />
                        <input type="hidden" name="txt_roll_id" id="txt_roll_id" style="width:50px;" class="text_boxes" readonly />
                        <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;" class="text_boxes" />
                    </td>
                </tr>
                <tr>
                    <td id="service_source_caption">Service Source</td>
                    <td>
						<?
                        echo create_drop_down( "cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/subcon_dyeing_production_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );search_populate(this.value);","","1,3" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td  id="service_company_caption">Service Company</td>
                    <td id="dyeing_company_td">
						<?
                        	echo create_drop_down( "cbo_service_company", 135, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="search_by_th_up">Received Challan</td>
                    <td>
                    	<input type="text" name="txt_recevied_chalan" id="txt_recevied_chalan"  class="text_boxes" style="width:122px;"   />
                    </td>
                </tr>
                <tr>
                    <td>Process </td>
                    <td id="sub_process_td">
						<?
                        	echo create_drop_down( "txt_process_id", 135, $conversion_cost_head_array,"", 0, "", 31, "","","","","","1,2,3,4,101,120,121,122,123,124");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="ltb_ltb_caption">LTB/BTB </td>
                    <td>
						<? $ltb_btb=array(1=>'BTB',2=>'LTB');
                        	echo create_drop_down( "cbo_ltb_btb", 135, $ltb_btb,"", 1, "-- Select --", 0, "","","","","","");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="">Water Flow</td>
                    <td>
                    	<input type="text" name="txt_water_flow" id="txt_water_flow" style="width:122px;" class="text_boxes_numeric"   />
                    </td>
                </tr>
                <tr>
                    <td id="production_date_td">Production Date</td>
                    <td>
                    	<input type="text" name="txt_process_end_date" id="txt_process_end_date" class="datepicker" style="width:122px;" readonly value="<?=date("d-m-Y");?>"/>
                    </td>
                </tr>
                <tr>
                    <td id="process_end_date">Process End Date</td>
                    <td>
                    	<input type="text" name="txt_process_date" id="txt_process_date" class="datepicker" style="width:122px;" readonly value="<?=date("d-m-Y");?>"/>
                    </td>
                </tr>
                <tr>
                    <td id="process_end_time">Process End Time</td>
                    <td>
                        <input type="text" name="txt_end_hours" id="txt_end_hours" class="text_boxes_numeric" placeholder="Hours" style="width:50px;" onBlur="fnc_move_cursor(this.value,'txt_end_hours','txt_end_date',2,23)" value="<? echo date('H');?>"  />
                        <input type="text" name="txt_end_minutes" id="txt_end_minutes" class="text_boxes_numeric" placeholder="Minutes" style="width:50px;" onBlur="fnc_move_cursor(this.value,'txt_end_minutes','txt_end_date',2,59)" value="<? echo date('i');?>" />
                    </td>
                </tr>
                <tr>
                    <td  id="floor_caption">Floor</td>
                    <td id="floor_td">
						<?
                        	echo create_drop_down( "cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0,"","","","",4 );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="machine_caption">Machine Name</td>
                    <td id="machine_td">
						<?
                       		echo create_drop_down("cbo_machine_name", 135, $blank_array,"", 1, "-- Select Machine --", 0, "",1 ); 
                        ?>
                    </td>
                </tr>
                <tr>
                <td id="result_caption">Result</td>
                    <td>
						<?
                        	echo create_drop_down("cbo_result_name", 135, $dyeing_result,"", 1, "-- Select Result --", 0, "",0 ,"1,2,3,4,5,6","","","",""); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="result_caption">Fabric Type</td>
                    <td>
						<? 
                        	echo create_drop_down( "cbo_fabric_type", 135, $fabric_type_for_dyeing,"", 1, "-- Select --", 0, "","","","","","");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Shift Name</td>
                    <td>
						<?
                        	echo create_drop_down("cbo_shift_name", 135, $shift_name,"", 1, "-- Select Shift --", 0, "",0 ,"","","","",""); 
                        ?>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="txt_process_start_date" id="txt_process_start_date" />
            <input type="hidden" name="txt_start_minutes" id="txt_start_minutes" class="text_boxes_numeric"  />
            <input type="hidden" name="cbo_ltb_btb" id="cbo_ltb_btb" class="text_boxes_numeric"  />
		</fieldset>
	<?    				
	} 
	?>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
	exit();
}
if ($action == "sys_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);


	?>

    <script>
        function js_set_value(sys_number) {
            //alert(sys_number);
            $("#hidden_sys_number").val(sys_number); // mrr number
            parent.emailwindow.hide();
        }
    </script>

    </head>

    <body>
    <div align="center" style="width:100%;">
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                <tr>
                    <th>Company</th>
                    <th>Functional Batch No</th>
                    <th>Batch No</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
                               class="formbutton"/></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td align="center">
						<?
						echo create_drop_down("cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $company, "", 0);
						?>
                    </td>

                    <td width="" align="center">
                        <input type="text" style="width:140px" class="text_boxes" name="txt_system_no"
                               id="txt_system_no" value="<? echo $system_no; ?>"/>
                    </td>
                    <td width="" align="center">
                        <input type="text" style="width:140px" class="text_boxes" name="txt_batch_no" id="txt_batch_no"
                               value="<? echo $batch_no; ?>"/>
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show"
                               onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_batch_no').value+'_'+'<? echo $load_unload; ?>', 'create_sys_search_list_view', 'search_div', 'subcon_dyeing_production_controller', 'setFilterGrid(\'list_view\',-1)')"
                               style="width:100px;"/>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="40" valign="middle" colspan="5">
						<? //echo load_month_buttons(1);
						?>
                        <!-- Hidden field here-------->
                        <input type="hidden" id="hidden_sys_number" value="hidden_sys_number"/>
                        <input type="hidden" id="hidden_update_id" value="hidden_update_id"/>
                        <!-- ---------END------------->
                    </td>
                </tr>
                </tbody>
                </tr>
            </table>
            <br>
            <div align="center" valign="top" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}


if ($action == "create_sys_search_list_view") {
	$ex_data = explode("_", $data);
	$company = $ex_data[0];
	$system_no = $ex_data[1];
	$batch_no = $ex_data[2];
	$load_unload = $ex_data[3];
	//echo $load_unload;
	//echo $fromDate;die;
	$sql_cond = "";

	/*if($db_type==2)
		{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'mm-dd-yyyy','-',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','-',1)."'";
		}
	if($db_type==0)
		{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}*/
	if (str_replace("'", "", $company) != 0) $com_cond = " and company_id=" . str_replace("'", "", $company) . " "; else $com_cond = "";

	if (str_replace("'", "", $system_no) != "") $sys_cond = "and system_no =" . str_replace("'", "", $system_no) . "  "; else  $sys_cond = "";
	if (str_replace("'", "", $batch_no) != '') $batch_no_cond = "and batch_no='$batch_no'  "; else  $batch_no_cond = "";
	if (str_replace("'", "", $load_unload) != '') $load_unload_cond = "and load_unload_id in($load_unload)  "; else  $load_unload_cond = "";


	$sql = "select id,batch_no,company_id,load_unload_id,batch_id,system_no,process_end_date 
			from  pro_fab_subprocess where entry_form=38 and status_active=1 and is_deleted=0 $com_cond $sys_cond $batch_no_cond  $load_unload_cond order by id desc";
	//echo $sql;
	$company_name_arr = return_library_array("select id, company_name from  lib_company", 'id', 'company_name');
	$arr = array(0 => $company_name_arr, 5 => $yes_no);
	echo create_list_view("list_view", "Company,Functional Batch No,Batch No,Process Start/Prod Date", "150,120,150,100", "650", "260", 0, $sql, "js_set_value", "id,system_no,batch_id,batch_no,load_unload_id,", "", 1, "company_id,0,0,0,", $arr, "company_id,system_no,batch_no,process_end_date", "", '', '0,0,0,3,0');
	exit();

}
if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	//print_r($data);
	$company_id=$data[1];
	// $company_id
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_service_company", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --","", "load_drop_down('requires/subcon_dyeing_production_controller', this.value, 'load_drop_floor', 'floor_td' )","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_service_company", 135, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_service_company", 135, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}

/*if($action=="populate_data_from_data")
{
	$sql = "select id, company_id, recv_number_prefix_num, dyeing_source, dyeing_company, receive_date, batch_id, process_id from inv_receive_mas_batchroll where id=$data and entry_form=63 and status_active=1 and is_deleted=0 ";
	//echo $sql;
	if($db_type==2) $group_concat="listagg(roll_id ,',') within group (order by roll_id) as roll_id ";
	else if($db_type==0) $group_concat="group_concat(roll_id)  as roll_id ";
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_issue_chalan').val('".$row[csf("recv_number_prefix_num")]."');\n";
		echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";
		echo "load_drop_down( 'requires/subcon_dyeing_production_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
		
		$roll_id_concat = return_field_value("$group_concat","pro_grey_batch_dtls","mst_id='".$data."' and roll_id>0 ","roll_id");
		$all_roll_concat=implode(",",array_unique(explode(",",$roll_id_concat))); 
		echo "$('#txt_roll_id').val('".$all_roll_concat."');\n";
		echo "$('#txt_issue_mst_id').val(".$row[csf("id")].");\n";
  	}
	exit();	
}*/

/*if($action=="check_issue_challan_no")
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
}*/

if($action=="process_name_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../", 1, 1,'','','');
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
                    $i=1; $process_row_id=''; $not_process_id_print_array=array(1,2,3,4,101,120,121,122,123,124);
					$hidden_process_id=explode(",",$txt_process_id);
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						if(!in_array($id,$not_process_id_print_array))
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

if($action=="process_name_popup_unload")
{
	echo load_html_head_contents("Batch Number Info", "../../", 1, 1,'','','');
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
                    $i=1; $process_row_id=''; $not_process_id_print_array=array();
					//echo $txt_process_id;die;
					$hidden_process_id=explode(",",$txt_process_id);
					/*$process_name='';
					$process_id_array=explode(",",$r_batch[csf("process_id")]);
					foreach($process_id_array as $val)
					{
						if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}*/
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						if(in_array($id,$hidden_process_id))
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

/*if ($action=="issue_challan_popup")
{
	echo load_html_head_contents("Issue Info", "../../", 1, 1,'','','');
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
                       		 echo create_drop_down( "cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'dyeing_production_controller',this.value+'**'+$cbo_company_id,'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
						?>
                    </td> 
                     <td id="dyeing_company_td">
                    	<?
                                echo create_drop_down( "cbo_service_company", 135, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                        ?>
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_challan_search_list_view', 'search_div', 'subcon_dyeing_production_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}*/

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and a.recv_number_prefix_num like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	// $sql = "select id, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date, process_id, batch_id from inv_receive_mas_batchroll where entry_form=63 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"; 
	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, a.process_id, a.batch_id,b.batch_no from inv_receive_mas_batchroll a,pro_batch_create_mst b where a.batch_id=b.id and a.entry_form=63 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond order by a.id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
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
                    <td width="100"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
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
?>
