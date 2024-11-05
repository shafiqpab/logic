<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_supplier")
{
    echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type in(1,7) and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- All --',0,'',0);
    //and b.party_type =9
    exit();
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);
	$cbo_item_cat = str_replace("'","",$cbo_item_cat);
	$cbo_year = str_replace("'","",$cbo_year);
	$txt_req_no = str_replace("'","",$txt_req_no);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$cbo_type = str_replace("'","",$cbo_type);

 

 	if($txt_date_from!="" && $txt_date_to!=""){$where_con=" and a.REQUISITION_DATE between '$txt_date_from' and '$txt_date_to'";}
 	if($cbo_supplier_id!=0){$where_con .=" and a.supplier_id = $cbo_supplier_id";}
 	if($cbo_item_cat!=0){$where_con .=" and a.ITEM_CATEGORY_ID = $cbo_item_cat";}
 	if($cbo_year!=0){$where_con .=" and to_char(a.insert_date,'YYYY') = $cbo_year";}
 	if($txt_req_no!=""){$where_con .=" and a.REQU_NO LIKE '%$txt_req_no'";}
	if($cbo_type!="" && $cbo_type!=0){$where_con .=" and a.READY_TO_APPROVE =$cbo_type";} 
	

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$designation_array = return_library_array( "SELECT id, custom_designation from lib_designation", "id", "custom_designation" );


	$user_name_array = array();
	$userData = sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];
	}

	$signatory_sql_res = sql_select("SELECT USER_ID, sequence_no, BYPASS from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=1 order by sequence_no");

	//$sql = "SELECT USER_ID, sequence_no, BYPASS from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=1 order by sequence_no";die;

	//echo $sql;die;

	foreach($signatory_sql_res as $sval)
	{
		$signatory_data_arr[$sval["USER_ID"]]=$sval["BYPASS"];
	}

	$rowspan = count($signatory_data_arr);

 


	// $sql ="SELECT a.ID,a.COMPANY_NAME, a.WO_NUMBER,a.WO_NUMBER_PREFIX_NUM, a.SUPPLIER_ID, a.WO_DATE
	// FROM wo_non_order_info_mst a, wo_non_order_info_dtls b
	// WHERE a.id=b.mst_id and a.company_name=$cbo_company_name and b.item_category_id not in(1,4,5,6,7,11,23) and a.status_active=1 and a.is_deleted=0 and a.READY_TO_APPROVED=1 $where_con group by a.ID,a.COMPANY_NAME, a.WO_NUMBER,a.WO_NUMBER_PREFIX_NUM, a.SUPPLIER_ID, a.WO_DATE ORDER by a.id";
	//echo $sql; die();

	$sql ="SELECT a.ID,a.COMPANY_ID, a.REQU_NO, a.REQUISITION_DATE, a.SUPPLIER_ID FROM inv_purchase_requisition_mst a WHERE a.COMPANY_ID=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $where_con";

	// echo $sql;die;
	
	$sql_result=sql_select($sql);
	foreach($sql_result as $row){
		$app_mst_id_arr[$row[ID]]=$row[ID];
	}



	$approved_no_array = array();
	$queryApp = "SELECT mst_id, approved_no, approved_by, approved_date, user_ip, entry_form from approval_history where entry_form=1 and un_approved_by=0 ".where_con_using_array($app_mst_id_arr,0,'mst_id')." order by id";
	 //echo $queryApp;
	$resultApp = sql_select($queryApp);
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_no')];
		$approved_date_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_date')];
		$approved_ip_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('user_ip')];
	}

	// echo "<pre>";
	// print_r($signatory_data_arr);die;

	$width=1020;


	ob_start();
	?>
        <fieldset style="width:<?=$width + 20;?>px;">
        	<table cellpadding="0" cellspacing="0" width="<?=$width;?>">
                <tr>
                   <td align="center" width="100%" colspan="9" style="font-size:20px"><strong><?=$report_title;?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><?=$company_arr[$cbo_company_name];?></strong></td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width;?>" class="rpt_table" align="left">
                <thead>
                    <th width="35">SL</th>
                    <th width="80">Requsition  No</th>
                    <th width="120">Supplier Name</th>
                    <th width="80">Requistion . Date</th>
                    <th width="140">Signatory</th>
                    <th width="130">Designation</th>
                    <th  width="50">Can Bypass</th>
                    <th width="100">IP Address</th>
                    <th width="80">App. Date</th>
                    <th width="60">App. Time</th>
                    <th>App. No</th>
                </thead>
            </table>
			<div style="width:<?=$width + 20;?>px; overflow-y:scroll; max-height:310px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width;?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
						$i=1;
						foreach($sql_result as $row){
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
							<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?= $i;?>','<?= $bgcolor;?>')" id="tr_<?=$i;?>">
								<td width="35" valign="middle" align="center" rowspan="<?= $rowspan;?>"><?=$i;?></td>
								<td width="80" rowspan="<?= $rowspan;?>" valign="middle" align="center"><?= $row["REQU_NO"];?></td>
								<td width="120" rowspan="<?= $rowspan;?>" valign="middle"><?= $supplier_arr[$row["SUPPLIER_ID"]];?></td>
								<td width="80" rowspan="<?= $rowspan;?>" valign="middle" align="center" ><?=change_date_format($row["REQUISITION_DATE"]);?></td>
								<?
								$flag=0;
								foreach($signatory_data_arr as $signator=>$bypass){
									if($flag==1){echo "<tr bgcolor='".$bgcolor."'>";}
								?>
										<td width="140"><p><?= $user_name_array[$signator]['full_name'] . " (" . $user_name_array[$signator]['name'] . ")";?></p></td>
										<td width="130"><p><?= $user_name_array[$signator]['designation'];?></p></td>
										<td width="50" align="center"><?= $yes_no[$bypass];?></td>
										<td width="100" align="center"><?= $approved_ip_array[$row["ID"]][$signator];?></td>
										<td width="80" align="center"><? if($approved_date_array[$row["ID"]][$signator]){echo date('d-m-y',strtotime($approved_date_array[$row["ID"]][$signator]));} ?></td>
										<td width="60" align="center"><? if($approved_date_array[$row["ID"]][$signator]){echo date('h-i-s',strtotime($approved_date_array[$row["ID"]][$signator]));} ?></td>
										<td align="center"><?= $approved_no_array[$row["ID"]][$signator];?></td>
							
								<?
								$flag=1;
							    }?>
								</tr>
								<?php
							$i++;
                        } 
						?>

                    </tbody>
                </table>
			</div>
      	</fieldset>
	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();
}



//-------------------------------------------------------


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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_quotation_style_no_search_list_view', 'search_div', 'other_purchase_work_order_approval_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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