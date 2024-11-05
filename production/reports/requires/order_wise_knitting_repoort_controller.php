<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//--------------------------------------------------------------------------------------------------------------------

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'order_wise_knitting_repoort_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd", "-")."'";
	}
	else
	{
		$date_cond="";
	}
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
		
	$sql= "select b.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Shipment Date", "120,120,80,150,120","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,3','',1) ;
	
   exit(); 
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	echo "Later"; die;
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
 	if($template==1)
	{
		$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
		
		$po_array=array(); $all_po_id='';
		$poDataArray=sql_select("select b.id, po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and b.pub_shipment_date=$txt_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $season_cond");// and a.season like '$txt_season'
		
		foreach($poDataArray as $row)
		{
			$po_array[$row[csf('id')]]=$row[csf('po_number')];
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}
		
		$noOfPo=count($poDataArray);
		$width=$noOfPo*2*90+350;
		ob_start();
		?>
        <fieldset style="width:<? echo $width+20; ?>px;">
        	<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>">
                <tr>
                   <td align="center" width="100%" colspan="<? echo $noOfPo*2+5 ?>" class="form_caption"><? echo $report_title; ?></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
                <thead>
                	<tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Color & Code</th>
                        <?
							foreach($po_array as $po_id=>$po_no)
							{
							?>
                            	<th width="180" colspan="2"><p><? echo $po_no; ?></p></th>
                            <?	
							}
						?>
                    	<th colspan="2">Color Total</th> 
                    </tr>
                    <tr>
                    	<?
							for($z=1;$z<=$noOfPo;$z++)
							{
							?>
                            	<th width="90">Bkg</th>
                                <th width="90">Knit</th>
                            <?	
							}
						?>
                        <th width="90">Bkg</th>
                        <th>Knit</th>
                    </tr>
                </thead>
            </table>
			<div style="width:<? echo $width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-18; ?>" class="rpt_table" id="table_body">
					<? 
						$po_knit_color_array=array();
						$knitDataArray=sql_select( "select po_breakdown_id, color_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=2 and status_active=1 and is_deleted=0 and po_breakdown_id in($all_po_id) group by color_id, po_breakdown_id");
						foreach($knitDataArray as $row_knit)
						{
							$po_knit_color_array[$row_knit[csf('color_id')]][$row_knit[csf('po_breakdown_id')]]=$row_knit[csf('quantity')];
						}
						
                        $i=1; $s=1; $po_color_array=array();
						$sql="select b.fabric_color_id,";
						
						foreach($po_array as $po_id=>$po_no)
						{
							if($s!=1) $addcomma=","; else $addcomma="";
							$sql.="$addcomma sum(case when b.po_break_down_id=$po_id then grey_fab_qnty else 0 end) as qnty$s";
							$s++;
						}
						
						$sql.=" from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($all_po_id) group by b.fabric_color_id";	
                      	//echo $sql;
                        $nameArray=sql_select( $sql);
                        foreach ($nameArray as $row)
                        {
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                       	?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                <td width="40"><? echo $i; ?></td>
                                <td width="100"><p><? echo $color_library[$row[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
                                <?
									$color_tot_qnty=0; $color_knit_tot_qnty=0; $z=1;
									foreach($po_array as $po_id=>$po_no)
									{
									?>
                                        <td width="90" align="right"><a href="##" onClick="openmypage(<? echo $po_id.",".$row[csf('fabric_color_id')]; ?>);"><? echo number_format($row[csf('qnty'.$z)],2,'.',''); ?></a></td>
                                        <td width="90" align="right"><? echo number_format($po_knit_color_array[$row[csf('fabric_color_id')]][$po_id],2,'.',''); ?></td>
                                    <?
										$color_tot_qnty+=$row[csf('qnty'.$z)];
										$color_knit_tot_qnty+=$po_knit_color_array[$row[csf('fabric_color_id')]][$po_id];
										
										$po_color_array[$po_id]['bkg']+=$row[csf('qnty'.$z)];
										$po_color_array[$po_id]['knit']+=$po_knit_color_array[$row[csf('fabric_color_id')]][$po_id];
										$z++;	
									}
								?>
                                <td align="right" width="90"><? echo number_format($color_tot_qnty,2,'.',''); ?></td>
                                <td align="right"><? echo number_format($color_knit_tot_qnty,2,'.',''); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
					?>
                	<tfoot>
                        <th width="100" colspan="2" align="right">Total</th>
                        <?
							$tot_bkg_qnty=0; $tot_knit_qnty=0;
							foreach($po_array as $po_id=>$po_no)
							{
							?>
                            	<th width="90" align="right"><? echo number_format($po_color_array[$po_id]['bkg'],2,'.',''); ?></th>
                                <th width="90" align="right"><? echo number_format($po_color_array[$po_id]['knit'],2,'.',''); ?></th>
                            <?
								$tot_bkg_qnty+=$po_color_array[$po_id]['bkg'];
								$tot_knit_qnty+=$po_color_array[$po_id]['knit'];	
							}
						?>
                        <th width="90" align="right"><? echo number_format($tot_bkg_qnty,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($tot_knit_qnty,2,'.',''); ?></th>
                	</tfoot>    
				</table> 
			</div>
      	</fieldset>      
	<?
	}
	
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
	echo "$total_data####$filename";
	
	disconnect($con);
	exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
	<fieldset style="width:840px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="90">Boby Part</th>
                <th width="90">Color Type</th>
                <th width="120">Construction</th>
                <th width="140">Composition</th>
                <th width="70">GSM</th>
                <th width="70">Fin Dia</th>
                <th width="90">Open/Tube</th>
                <th>Qnty</th>
            </thead>
         </table>
         <div style="width:830px; max-height:270px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="812" cellpadding="0" cellspacing="0">
                <?
                $i=1; $total_qnty=0;
                $sql="select c.body_part_id, c.color_type_id, c.construction, c.composition, c.gsm_weight, c.width_dia_type, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_booking_mst b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.item_category in(2,13) and a.pre_cost_fabric_cost_dtls_id=c.id and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id=$po_id and a.fabric_color_id=$color_id group by c.body_part_id, c.color_type_id, c.construction, c.composition, c.gsm_weight, c.width_dia_type, a.dia_width";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
                    $total_qnty+=$row[csf('qnty')];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="90"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</td>
                        <td width="90"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $row[csf('construction')]; ?>&nbsp;</p></td>
                        <td width="140"><p><? echo $row[csf('composition')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
                        <td width="90"><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tfoot>
                    <th colspan="8" align="right">Total</th>
                    <th align="right"><? echo number_format($total_qnty,2,'.',''); ?>&nbsp;</th>
                </tfoot>
            </table>
        </div>	
	</fieldset>   
<?
exit();
}
?>