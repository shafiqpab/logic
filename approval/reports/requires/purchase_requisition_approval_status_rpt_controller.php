<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

if($action=="print_button_variable_setting") //Report Setting
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=12 and report_id=225 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($tittle." Info", "../../../", 1, 1,'','','');	
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
				selected_id.push( str[4] );
				selected_name.push( str[1] );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[4] ) break;
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

			$('#hide_id').val(id);
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
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter <? echo $tittle; ?> </th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
	                    <input type="hidden" name="hide_no" id="hide_no" value="" />
	                    <input type="hidden" name="hide_id" id="hide_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                    	<?
							    $search_by_arr=array(1=>"Requistion No",2=>"System No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>
	                        <td align="center" id="search_by_td">
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
	                        </td> 	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+<? echo $type; ?>, 'create_requisition_no_search_list_view', 'search_div', 'purchase_requisition_approval_status_rpt_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_requisition_no_search_list_view")
{
    $data=explode('**',$data);
    //print_r($data);
	$company_id=$data[0];
	$search_by=$data[1];
	$search_string=trim($data[2]);
	$arr=array (0=>$company_arr);
	if($search_by==1)
	{
        if ($search_string != "") {
            $search_field_cond=" and requ_prefix_num in($search_string)"; 
        }else{
            $search_field_cond=""; 

        }
	} else if($search_by==2)
	{
        if ($search_string != "") {
            $search_field_cond=" and id = $search_string";
        }else{
            $search_field_cond=""; 

        }
		$search_field_cond=" and id= $search_string"; 
	} else{
        $search_field_cond=""; 
    }
		
	
    $sql= "SELECT id,requ_no,requ_prefix_num,company_id,requisition_date from inv_purchase_requisition_mst where status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond order by id desc";
    //echo $sql;
	echo create_list_view("tbl_list_search", "Importer,Requisition No, Requisition Date,Prefix Num,System ID", "120,120,120,100,120","700","170",0, $sql , "js_set_value", "requ_no,requisition_date,requ_prefix_num,id", "", 1, "company_id,0,0,0", $arr , "company_id,requ_no,requisition_date,requ_prefix_num,id", "",'','0,0,0,0','',1);
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
	$txt_requistion_no = str_replace("'","",$txt_requistion_no);
	$hide_requistion_id = str_replace("'","",$hide_requistion_id);

 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and a.requisition_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
			$date_cond=" and to_char(c.approved_date,'dd-Mon-YYYY') between $txt_date_from and $txt_date_to";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
		}
	}

	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	
	$requistion_cond="";
	if($hide_requistion_id != "") $requistion_cond=" and a.id in(".implode("','",explode("*",$hide_requistion_id)).")";
	
	//$dealing_merchant_array = return_library_array("SELECT id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$store_array = return_library_array( "select a.id,a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id=$cbo_company_name and a.status_active=1 and b.category_type not in(1,2,3,12,13,14,24,25) group by a.id,a.store_name order by a.store_name", "id", "store_name" );
	
	$user_name_array = array();
	$userData = sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}

	$approved_no_array = array();
	$queryApp = "SELECT mst_id, approved_by, approved_no from approval_history where entry_form=1 and un_approved_by=0";
	$resultApp = sql_select($queryApp);
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_no')];
	}
	
	//$buyer_id_arr = return_library_array( "SELECT user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=10 and bypass=2", "user_id", "buyer_id" );	
	$signatory_data_arr = sql_select("SELECT user_id, sequence_no, bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=1 order by sequence_no");
	foreach($signatory_data_arr as $sval)
	{
		$signatory_main[$sval[csf('user_id')]]=$sval[csf('bypass')];
	}

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array();
	$query = "SELECT mst_id, approved_no, approved_by, approved_date, user_ip, entry_form from approval_history where entry_form=1 and (un_approved_by=0 or un_approved_by is null)";
	$result = sql_select($query);
	foreach($result as $row)
	{
		$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_by')]] = $row[csf('approved_date')];
		$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_by')]] = $row[csf('user_ip')];
		$approved_date = date("Y-m-d",strtotime($row[csf('approved_date')]));
		$approved_time = date("h:i:sa",strtotime($row[csf('approved_date')]));
		
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
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id =39 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);
	if($format_ids[0]==120) // Print Report 2 //121,122,123,129,169,227,118,119
	{
		$print_type=3;
	}
	elseif($format_ids[0]==121) // Print Report 2 //121,122,123,129,169,227,118,119
	{
		$print_type=4;
	}
	elseif($format_ids[0]==122) // Print Report 3
	{	
		$print_type=5;
	}
	elseif($format_ids[0]==123) // Print Report 4
	{
		$print_type=6;
	}
	elseif($format_ids[0]==129) // Print Report 5
	{
		$print_type=7;
	}
	elseif($format_ids[0]==169) // Print Report 6
	{ 
		$print_type=8;
	}
	elseif($format_ids[0]==227) // Print Report 8
	{
		$print_type=10;
	}
	elseif($format_ids[0]==118) // Print Report With Group
	{
		$print_type=1;
	}
	elseif($format_ids[0]==119) // Print Report Without Group
	{
		$print_type=2;
	}
	elseif($format_ids[0]==241) // Print Report Without Group
	{
		$print_type=11;
	}
	elseif($format_ids[0]==165) // Print Report Without Group
	{
		$print_type=12;
	}
	elseif($format_ids[0]==580) // Print Report Without Group
	{
		$print_type=13;
	}
	elseif($format_ids[0]==28) // Print Report Without Group
	{
		$print_type=14;
	}
	elseif($format_ids[0]==280) // Print Report Without Group
	{
		$print_type=15;
	}
	elseif($format_ids[0]==688) // Print Report Without Group
	{
		$print_type=16;
	}
	elseif($format_ids[0]==243) // Print Report Without Group
	{
		$print_type=17;
	}
	elseif($format_ids[0]==310) // Print Report Without Group
	{
		$print_type=18;
	}
	elseif($format_ids[0]==304) // Print Report Without Group
	{
		$print_type=19;
	}
	elseif($format_ids[0]==719) // Print Report Without Group
	{
		$print_type=20;
	}
	elseif($format_ids[0]==723) // Print Report Without Group
	{
		$print_type=21;
	}
	elseif($format_ids[0]==339) // Print Report Without Group
	{
		$print_type=22;
	}
	elseif($format_ids[0]==370) // Print Report Without Group
	{
		$print_type=23;
	}
	elseif($format_ids[0]==235) // Print Report Without Group
	{
		$print_type=24;
	}
	elseif($format_ids[0]==382) // Print Report Without Group
	{
		$print_type=25;
	}
	elseif($format_ids[0]==768 ) // Print Report Without Group
	{
		$print_type=26;
	}
	elseif($format_ids[0]==425  ) // Print Report Without Group
	{
		$print_type=27;
	}
	else{
		$print_type=0;   
	}
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
					<?
						if($type==2){
					?>
                    <th width="40">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="90">Requisition Value</th>
                    <th width="90">Requisition Date</th>
                    <th width="110">Req. By:</th>
                    <th width="80">Store Name</th>
                    <th width="140">Signatory</th>
                    <th width="100">Designation</th>
                    <th width="90">Approval Date</th>
                    <th width="90">Approval Time</th>
                    <th>Approve No</th>
					<?
						}else{
					?>
					<th width="40">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="90">Requisition Value</th>
                    <th width="90">Requisition Date</th>
                    <th width="110">Req. By:</th>
                    <th width="80">Store Name</th>
					<?
						}
					?>
                </thead>
            </table>
			<div style="width:1020px; overflow-y:scroll; max-height:310px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
						$rowspanMain=count($signatory_main);
						
						//echo $rowspanMain;die()
						$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
						
						if($type==2){ //Full Approved
							$approved_cond=" and a.is_approved=1";

							$sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $requistion_cond  $date_cond $approved_cond  group by a.id, a.company_id,  a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name  order by a.id desc";
						}elseif($type==1){ //Pending
							$approved_cond=" and a.is_approved in (0,3)";

							$sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount   from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $requistion_cond  $date_cond $approved_cond  group by a.id, a.company_id,  a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name order by a.id desc";
						}else
						{
							$approved_cond=" and a.is_approved=3";
						} 
						
						
						// echo $sql;
                        $sql_result=sql_select($sql);
						$j=1;
                        foreach ($sql_result as $row)
                        {
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if($cbo_date_by==3 && $from_date!="" && $to_date!="")
							{
								$max_approved_date=$max_approval_date_array[$row[csf('requisition_id')]];
								if($max_approved_date>=$from_date && $max_approved_date<=$to_date) $print_cond=1;
								else $print_cond=0;
							}
							else
							{
								$print_cond=1;
							}
							
							if(($type==2 && $row[csf('is_approved')]==1))
							{															
								foreach($signatory_main as $user_id=>$val)
								{
									$variable_no="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('requ_prefix_num')]." <a/>";
									$variable_amount="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('amount')]." <a/>";
								    ?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
									
										<td width="40"><? echo $i; ?></td>
										<td width="80"  align="center">
											<?= $variable_no; ?>
                                        </td>
										<td width="90" align="center">
											<?= $variable_amount; ?>
                                        </td>
										<td width="90"><p><?= $row[csf('requisition_date')]; ?>&nbsp;</p></td>
                                        <td width="110" ><p><?= $row[csf('req_by')]; ?>&nbsp;</p></td>

                                        <td width="80" align="center"><?= $store_array[$row[csf('store_name')]];?></td>

									<?
									
									
									$approved_no=''; $user_ip='';								
									$approval_date=$user_approval_array[$row[csf('requisition_id')]][$user_id];
									$user_ip=$user_ip_array[$row[csf('requisition_id')]][$user_id];
									if($approval_date!="") $approved_no=$approved_no_array[$row[csf('requisition_id')]][$user_id];					
									
									
									$date=''; $time=''; 
									if($approval_date!="") 
									{
										$date=date("d-M-Y",strtotime($approval_date)); 
										$time=date("h:i:s A",strtotime($approval_date)); 
									}
									?>
										<td width="140"><p><? echo $user_name_array[$user_id]['full_name']." (".$user_name_array[$user_id]['name'].")"; ?>&nbsp;</p></td>
                                        <td width="100"><p><? echo $user_name_array[$user_id]['designation']; ?>&nbsp;</p></td>			
										<td width="90" align="center">
											<?= $date;	?>
										</td>
                                        <td width="90" align="center"><p><?= $time; ?>&nbsp;</p></td>
                                        <td align="center"><p><? echo $approved_no; ?>&nbsp;</p></td>
									</tr>
								    <?
									$j++;
								}
							    
							}else if(($type==1 && $row[csf('is_approved')]==0) || ($type==1 && $row[csf('is_approved')]==3))
							{									
								foreach($signatory_main as $user_id=>$val)
								{
								    ?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="80"  align="center">
											<?= $row[csf('requ_prefix_num')]; ?>
                                        </td>
										<td width="90" align="center">
											<?= $row[csf('amount')]; ?>
                                        </td>
										<td width="90"><p><?= $row[csf('requisition_date')]; ?>&nbsp;</p></td>
                                        <td width="110" ><p><?= $row[csf('req_by')]; ?>&nbsp;</p></td>

                                        <td width="80" align="center"><?= $store_array[$row[csf('store_name')]];?></td>

									<?
									
									
									$approved_no=''; $user_ip='';								
									$approval_date=$user_approval_array[$row[csf('requisition_id')]][$user_id];
									$user_ip=$user_ip_array[$row[csf('requisition_id')]][$user_id];
									if($approval_date!="") $approved_no=$approved_no_array[$row[csf('requisition_id')]][$user_id];					
									
									
									$date=''; $time=''; 
									if($approval_date!="") 
									{
										$date=date("d-M-Y",strtotime($approval_date)); 
										$time=date("h:i:s A",strtotime($approval_date)); 
									}
									?>
										
									</tr>
								    <?
									$j++;
								}
							    
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

if($action=="report_generate_2")
{ 
	$process = array( &$_POST );

	extract(check_magic_quote_gpc( $process ));

	$type = str_replace("'","",$cbo_type);
	$txt_requistion_no = str_replace("'","",$txt_requistion_no);
	$hide_requistion_id = str_replace("'","",$hide_requistion_id);
	$cbo_company_name = str_replace("'","",$cbo_company_name);

 	$date_cond=""; $from_date=""; $to_date="";
 	if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	{
		if(str_replace("'","",trim($cbo_date_by))==1)
		{
			$date_cond=" and a.requisition_date between $txt_date_from and $txt_date_to";
		}
		else if(str_replace("'","",trim($cbo_date_by))==2)
		{
			// $a_cond=" and h.approved_date between $txt_date_from and $txt_date_to and h.current_approval_status=1";
			$a_cond=" and  to_date(to_char(h.approved_date,'dd-Mon-YYYY')) between $txt_date_from and $txt_date_to and h.current_approval_status=1";

			
			//$date_cond=" and a.requisition_date between $txt_date_from and $txt_date_to";
		}
		else
		{
			$from_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_from))));
			$to_date=date("Y-m-d",strtotime(str_replace("'","",trim($txt_date_to))));
		}
	}

	$cbo_date_by=str_replace("'","",trim($cbo_date_by));
	
	$requistion_cond="";
	if($hide_requistion_id != "") $requistion_cond=" and a.id in(".implode("','",explode("*",$hide_requistion_id)).")";
	
	//$dealing_merchant_array = return_library_array("SELECT id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$store_array = return_library_array( "select a.id,a.store_name from lib_store_location a, lib_store_location_category b  where a.id=b.store_location_id and a.is_deleted=0 and a.company_id=$cbo_company_name and a.status_active=1 and b.category_type not in(1,2,3,12,13,14,24,25) group by a.id,a.store_name order by a.store_name", "id", "store_name" );
	
	$user_name_array = array();
	$userData = sql_select( "SELECT id, user_name, user_full_name, designation from user_passwd");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row[csf('id')]]['name']=$user_row[csf('user_name')];
		$user_name_array[$user_row[csf('id')]]['full_name']=$user_row[csf('user_full_name')];
		$user_name_array[$user_row[csf('id')]]['designation']=$designation_array[$user_row[csf('designation')]];	
	}

	$approved_no_array = array();
	$queryApp = "SELECT mst_id, approved_by, approved_no from approval_history where entry_form=1 and un_approved_by=0";
	$resultApp = sql_select($queryApp);
	foreach ($resultApp as $row)
	{
		$approved_no_array[$row[csf('mst_id')]][$row[csf('approved_by')]]=$row[csf('approved_no')];
	}
	
	//$buyer_id_arr = return_library_array( "SELECT user_id, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=10 and bypass=2", "user_id", "buyer_id" );
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	
	$signatory_data_arr = sql_select("SELECT user_id, sequence_no, bypass from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=1 order by sequence_no");
	
	foreach($signatory_data_arr as $sval)
	{
		$signatory_main[$sval[csf('user_id')]]=$sval[csf('bypass')];
	}

	$user_approval_array=array(); $user_ip_array=array(); $max_approval_date_array=array();
	$query = "SELECT mst_id, approved_no, approved_by, approved_date, user_ip, entry_form from approval_history where entry_form=1 and (un_approved_by=0 or un_approved_by is null)";
	$result = sql_select($query);
	foreach($result as $row)
	{
		$user_approval_array[$row[csf('mst_id')]][$row[csf('approved_by')]] = $row[csf('approved_date')];
		$user_ip_array[$row[csf('mst_id')]][$row[csf('approved_by')]] = $row[csf('user_ip')];
		$approved_date = date("Y-m-d",strtotime($row[csf('approved_date')]));
		$approved_time = date("h:i:sa",strtotime($row[csf('approved_date')]));
		
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
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id =39 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);
	if($format_ids[0]==120) // Print Report 2 //121,122,123,129,169,227,118,119
	{
		$print_type=3;
	}
	elseif($format_ids[0]==121) // Print Report 2 //121,122,123,129,169,227,118,119
	{
		$print_type=4;
	}
	elseif($format_ids[0]==122) // Print Report 3
	{	
		$print_type=5;
	}
	elseif($format_ids[0]==123) // Print Report 4
	{
		$print_type=6;
	}
	elseif($format_ids[0]==129) // Print Report 5
	{
		$print_type=7;
	}
	elseif($format_ids[0]==169) // Print Report 6
	{ 
		$print_type=8;
	}
	elseif($format_ids[0]==227) // Print Report 8
	{
		$print_type=10;
	}
	elseif($format_ids[0]==118) // Print Report With Group
	{
		$print_type=1;
	}
	elseif($format_ids[0]==119) // Print Report Without Group
	{
		$print_type=2;
	}
	elseif($format_ids[0]==241) // Print Report Without Group
	{
		$print_type=11;
	}
	elseif($format_ids[0]==165) // Print Report Without Group
	{
		$print_type=12;
	}
	elseif($format_ids[0]==580) // Print Report Without Group
	{
		$print_type=13;
	}
	elseif($format_ids[0]==28) // Print Report Without Group
	{
		$print_type=14;
	}
	elseif($format_ids[0]==280) // Print Report Without Group
	{
		$print_type=15;
	}
	elseif($format_ids[0]==688) // Print Report Without Group
	{
		$print_type=16;
	}
	elseif($format_ids[0]==243) // Print Report Without Group
	{
		$print_type=17;
	}
	elseif($format_ids[0]==310) // Print Report Without Group
	{
		$print_type=18;
	}
	elseif($format_ids[0]==304) // Print Report Without Group
	{
		$print_type=19;
	}
	elseif($format_ids[0]==719) // Print Report Without Group
	{
		$print_type=20;
	}
	elseif($format_ids[0]==723) // Print Report Without Group
	{
		$print_type=21;
	}
	elseif($format_ids[0]==339) // Print Report Without Group
	{
		$print_type=22;
	}
	elseif($format_ids[0]==370) // Print Report Without Group
	{
		$print_type=23;
	}
	elseif($format_ids[0]==235) // Print Report Without Group
	{
		$print_type=24;
	}
	elseif($format_ids[0]==382) // Print Report Without Group
	{
		$print_type=25;
	}
	elseif($format_ids[0]==768 ) // Print Report Without Group
	{
		$print_type=26;
	}
	elseif($format_ids[0]==425  ) // Print Report Without Group
	{
		$print_type=27;
	}
	else{
		$print_type=0;   
	}
	ob_start();
 
	$mstIdCond="";
	if(str_replace("'","",trim($cbo_date_by))==2)
		{
			
			$app_sql_data=sql_select("SELECT MAX (h.approved_date) ,mst_id     FROM approval_history h
			WHERE h.entry_form = 1  $a_cond and h.current_approval_status=1  group by h.mst_id ");

                //  "SELECT MAX (h.approved_date) ,mst_id     FROM approval_history h
                //   WHERE    h.entry_form = 1  $a_cond and h.current_approval_status=1  group by h.mst_id "
		
			foreach($app_sql_data as $val){
				$mstIdArr[$val[csf('mst_id')]]=$val[csf('mst_id')];
			}
			$mstIds=implode(",",$mstIdArr);
			$mstIdCond="and a.id in ($mstIds)";
		}

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
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000"  class="rpt_table" align="left">
                <thead>
					<?
						if($type==2){
					?>
                    <th width="40">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="90">Requisition Value</th>
                    <th width="90">Item Category</th>
                    <th width="90">Department</th>
                    <th width="90">Requisition Date</th>
                    <th width="110">Req. By:</th>
                    <th width="80">Store Name</th>
                    <!-- <th width="140">Signatory</th>
                    <th width="100">Designation</th> -->
                    <th width="90">Approval Date</th>
                    <th width="90">Approval Time</th>
                    <!-- <th>Approve No</th> -->
					<?
						}else{
					?>
					<th width="40">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="90">Requisition Value</th>
					<th width="90">Item Category</th>
                    <th width="90">Department</th>
                    <th width="90">Requisition Date</th>
                    <th width="110">Req. By:</th>
                    <th width="80">Store Name</th>
					<?
						}
					?>
                </thead>
            </table>
			<div style="width:1020px; overflow-y:scroll; max-height:310px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						$i=1; //$signatory=explode(",",$signatory); $rowspan=count($signatory);
						$rowspanMain=count($signatory_main);						
						$bypass_no_user_id_main=explode(",",$bypass_no_user_id_main);
						
				
						
						if($type==2){ //Full Approved
							$approved_cond=" and a.is_approved=1";

							//  $sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id,a.department_id, a.requisition_date,listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $requistion_cond  $date_cond $approved_cond  group by a.id, a.company_id,a.department_id ,a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name  order by a.id desc";

							 $sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id,a.department_id, a.requisition_date,listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID ,(select max(h.approved_date) from approval_history h where a.id=h.mst_id and h.entry_form=1 $a_cond ) as approved_date, a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $requistion_cond  $date_cond $approved_cond $mstIdCond  group by a.id, a.company_id,a.department_id ,a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name  order by a.id desc";
						}elseif($type==1){ //Pending
							$approved_cond=" and a.is_approved in (0,2)";

							 $sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id,a.department_id, a.requisition_date,listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount   from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $requistion_cond  $date_cond $approved_cond  group by a.id, a.company_id,a.department_id ,a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name order by a.id desc";
						}else
						{
							$approved_cond=" and a.is_approved=3";

							$sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id,a.department_id, a.requisition_date,listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount   from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $requistion_cond  $date_cond $approved_cond  group by a.id, a.company_id, a.department_id,a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name order by a.id desc";
						} 
						
						
						//echo $sql;die();


                        $sql_result=sql_select($sql);

						//echo count($sql_result); die;
						$j=1;
                        foreach ($sql_result as $row)
                        {
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if($cbo_date_by==3 && $from_date!="" && $to_date!="")
							{
								$max_approved_date=$max_approval_date_array[$row[csf('requisition_id')]];
								if($max_approved_date>=$from_date && $max_approved_date<=$to_date) $print_cond=1;
								else $print_cond=0;
							}
							else
							{
								$print_cond=1;
							}
							
							if(($type==2 && $row[csf('is_approved')]==1))
							{															
								//foreach($signatory_main as $user_id=>$val)
								//{
									$variable_no="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('requ_prefix_num')]." <a/>";
									$variable_amount="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('amount')]." <a/>";
								    ?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
									
										<td width="40"><? echo $i; ?></td>
										<td width="80"  align="center">
											<?= $variable_no; ?>
                                        </td>
										<td width="90" align="center">
											<?= $row[csf('amount')]; ?>
                                        </td>
										<td  width="90"><?
									$item_name_arr=array();
									foreach(explode(',',$row[ITEM_CATEGORY_ID]) as $item_id){
										$item_name_arr[$item_id]=$item_category[$item_id];
									}
									echo implode(', ',$item_name_arr);?></p> </td>
									    <td  width="90"><a href="##" onClick="open_popup('<?=$cbo_company_name.'_'.$row[csf('requisition_id')].'_'.$row[csf('department_id')]; ?>','full_approved_popup')"><p><?= $department_arr[$row[csf('department_id')]]; ?> </td>
										<td width="90"><p><?= $row[csf('requisition_date')]; ?>&nbsp;</p></td>
                                        <td width="110" ><p><?= $row[csf('req_by')]; ?>&nbsp;</p></td>

                                        <td width="80" align="center"><?= $store_array[$row[csf('store_name')]];?></td>

									<?
									
									
									$approved_no=''; $user_ip='';	
									
									$approval_date=$row[csf('approved_date')];
									  //echo $approval_date;die();
									$approvall_date=$user_approval_array[$row[csf('requisition_id')]][$user_id];
									$user_ip=$user_ip_array[$row[csf('requisition_id')]][$user_id];
									if($approval_date!="") $approved_no=$approved_no_array[$row[csf('requisition_id')]][$user_id];					
									
									
									$date=''; $time=''; 
									if($approval_date!="") 
									{
										$date=date("d-M-Y",strtotime($approval_date)); 
										$time=date("h:i:s A",strtotime($approval_date)); 
									}
									?>
												 
										<td width="90" align="center">
											<?= $date;	?>
										</td>
                                        <td width="90" align="center"><p><?= $time; ?>&nbsp;</p></td>
                                       
									</tr>
								    <?
									//$j++;
								//}
							    
							}
							else if(($type==1 && $row[csf('is_approved')]==0) || ($type==1 && $row[csf('is_approved')]==3))
							{	
								$variable_no="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('requ_prefix_num')]." <a/>";
									$variable_amount="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('amount')]." <a/>";
								    
								//echo $row[csf('is_approved')]; die;
								// echo "<pre>";
								// print_r($signatory_main);

								//foreach($signatory_main as $user_id=>$val)
								//{
								    ?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="80"  align="center">
										<?= $variable_no; ?>
                                        </td>
										<td width="90" align="center">
											<?= $row[csf('amount')]; ?>
                                        </td>
									    <td  width="90"><?
									$item_name_arr=array();
									foreach(explode(',',$row[ITEM_CATEGORY_ID]) as $item_id){
										$item_name_arr[$item_id]=$item_category[$item_id];
									}
									echo implode(', ',$item_name_arr);?></p> </td>
									    <td  width="90"><a href="##" onClick="open_popup('<?=$cbo_company_name.'_'.$row[csf('requisition_id')].'_'.$row[csf('department_id')]; ?>','pending_popup')"><?=$department_arr[$row[csf('department_id')]];?> </td>
										<td width="90"><p><?= $row[csf('requisition_date')]; ?>&nbsp;</p></td>
                                        <td width="110" ><p><?= $row[csf('req_by')]; ?>&nbsp;</p></td>

                                        <td width="80" align="center"><?= $store_array[$row[csf('store_name')]];?></td>

									<?
									
									
									$approved_no=''; $user_ip='';								
									$approval_date=$user_approval_array[$row[csf('requisition_id')]][$user_id];
									$user_ip=$user_ip_array[$row[csf('requisition_id')]][$user_id];
									if($approval_date!="") $approved_no=$approved_no_array[$row[csf('requisition_id')]][$user_id];					
									
									
									$date=''; $time=''; 
									if($approval_date!="") 
									{
										$date=date("d-M-Y",strtotime($approval_date)); 
										$time=date("h:i:s A",strtotime($approval_date)); 
									}
									?>
										
									</tr>
								    <?
									//$j++;
								//}
							    
							}
							else if(($type==3 && $row[csf('is_approved')]==0) || ($type==3 && $row[csf('is_approved')]==3))
							{	
								$variable_no="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('requ_prefix_num')]." <a/>";
									$variable_amount="<a href='#' onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('requisition_id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks')]."','".$print_type."')\"> ".$row[csf('amount')]." <a/>";
								    
								//echo $row[csf('is_approved')]; die;
								// echo "<pre>";
								// print_r($signatory_main);

								//foreach($signatory_main as $user_id=>$val)
								//{
								    ?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="80"  align="center">
										<?= $variable_no; ?>
                                        </td>
										<td width="90" align="center">
											<?= $row[csf('amount')]; ?>
                                        </td>
										<td  width="90"><?
									$item_name_arr=array();
									foreach(explode(',',$row['ITEM_CATEGORY_ID']) as $item_id){
										$item_name_arr[$item_id]=$item_category[$item_id];
									}
									echo implode(', ',$item_name_arr);?></p> </td>
									    <td  width="90"><a href="##" onClick="open_popup('<?=$cbo_company_name.'_'.$row[csf('requisition_id')].'_'.$row[csf('department_id')]; ?>','partial_popup')"><?=$department_arr[$row[csf('department_id')]];?></td>
										<td width="90"><p><?= $row[csf('requisition_date')]; ?>&nbsp;</p></td>
                                        <td width="110" ><p><?= $row[csf('req_by')]; ?>&nbsp;</p></td>

                                        <td width="80" align="center"><?= $store_array[$row[csf('store_name')]];?></td>

									<?
									
									
									$approved_no=''; $user_ip='';								
									$approval_date=$user_approval_array[$row[csf('requisition_id')]][$user_id];
									$user_ip=$user_ip_array[$row[csf('requisition_id')]][$user_id];
									if($approval_date!="") $approved_no=$approved_no_array[$row[csf('requisition_id')]][$user_id];					
									
									
									$date=''; $time=''; 
									if($approval_date!="") 
									{
										$date=date("d-M-Y",strtotime($approval_date)); 
										$time=date("h:i:s A",strtotime($approval_date)); 
									}
									?>
										
									</tr>
								    <?
									//$j++;
								//}
							    
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

if($action=="full_approved_popup")
{   extract($_REQUEST);
	list($company_id,$req_id,$department_id)=explode('_',$data);
	echo load_html_head_contents($tittle." Info", "../../../", 1, 1,'','','');	
 
	$Approval_status = array(1 => "Approved", 0 => "Pending", 2 => "Pending", 3 => "Partial Approved");	

	$sql="select c.DEPARTMENT_ID, a.USER_ID, c.IS_APPROVED, b.USER_NAME, b.USER_FULL_NAME, max(a.APPROVED_DATE) as APPROVED_DATE from user_passwd b, electronic_approval_setup a, inv_purchase_requisition_mst c where b.id=a.user_id and a.company_id=c.company_id and a.company_id=$company_id and c.id=$req_id and a.user_id in($users) and c.status_active=1 and c.is_deleted=0 $approved_cond group by c.department_id, a.user_id, c.is_approved, b.user_name, b.user_full_name order by b.USER_NAME";

	$sql="select a.USER_ID, b.USER_NAME, b.USER_FULL_NAME from electronic_approval_setup a, user_passwd b where a.user_id=b.id and a.company_id=$company_id and page_id in(813,2302) and a.is_deleted=0 and b.valid=1 group by a.user_id, b.user_name, b.user_full_name order by b.user_name";
	//echo $sql; 
	$dataArr=sql_select($sql);

	$hisSql =  "select APPROVED_BY, APPROVED_DATE, UN_APPROVED_BY, UN_APPROVED_DATE from APPROVAL_HISTORY where entry_form=1 and mst_id in(".$req_id.") order by id desc";
	$hisSqlRes=sql_select($hisSql);
	$check_approval_user_arr=array();
	$approval_user_info=array();
	foreach($hisSqlRes as $row)
	{
		if ($check_approval_user_arr[$row['APPROVED_BY']] == "")
		{
			if ($row['UN_APPROVED_DATE'] != "") 
			{
				$approval_user_info[$row['APPROVED_BY']]['approval_date']=$row['UN_APPROVED_DATE'];
				$approval_user_info[$row['APPROVED_BY']]['approved']=0;
			}
			else 
			{
				$approval_user_info[$row['APPROVED_BY']]['approval_date']=$row['APPROVED_DATE'];
				$approval_user_info[$row['APPROVED_BY']]['approved']=1;
			}

			$check_approval_user_arr[$row['APPROVED_BY']]=$row['APPROVED_BY'];
		}		
	}
	//echo '<pre>';print_r($approval_user_info);
    ?>
    <div  id="data_panel" align="center" style="width:99%">
		<fieldset style="width: 100%">
		<table width="100%" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
			<thead>
				<tr>
					<th width="25">Sl</th>
					<th>Approval Person</th>
					<th width="100">Approval Status</th>
					<th width="80" >Approval Date</th>
					<th width="80">Approval Time</th>
				</tr>
			</thead> 
       		<?
			$i=1;	  
			foreach ($dataArr as $row) 
			{
				?>
				<tr bgColor="<?= $bgcolor;?>">
					<td><?=$i;?></td>
					<td><? echo $row[csf('USER_FULL_NAME')];?> (<? echo $row[csf('USER_NAME')];?>)</td>
					<td align="center"><? echo $Approval_status[$approval_user_info[$row['USER_ID']]['approved']]; ?></td>
					<td align="center"><? if ($approval_user_info[$row['USER_ID']]['approval_date']) echo date('d-m-Y',strtotime($approval_user_info[$row['USER_ID']]['approval_date'])); ?></td>
					<td align="center"><? if ($approval_user_info[$row['USER_ID']]['approval_date']) echo date('h:i:s a',strtotime($approval_user_info[$row['USER_ID']]['approval_date']));?></td>
				</tr>
				<?
				$i++;
			}
			?>
      	</table>
    	</fieldset>
  	</div>
    <?
	exit();
}

if($action=="pending_popup_________________off")
{   extract($_REQUEST);
	echo load_html_head_contents($tittle." Info", "../../../", 1, 1,'','','');	
    //echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    //extract($_REQUEST);
    //$key=$row['wo_pi_no'].'**'.$row['item_description'].'**'.$row['order_uom'];
   
	// $sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $requistion_cond  $date_cond $approved_cond  group by a.id, a.company_id,  a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name  order by a.id desc";
	$Approval_status = array(0 => "pending",1 => "Approved", 2 => "Un_approved", 3 => "Partial Approved");
   
	$approved_cond=" and c.is_approved in (0)";
	$sql="select a.id,a.USER_ID,a.APPROVED_BY,a.COMPANY_ID,a.APPROVED_DATE,c.COMPANY_ID,c.is_approved,b.id,b.id,b.USER_NAME,b.USER_FULL_NAME from user_passwd b ,electronic_approval_setup a,inv_purchase_requisition_mst c where b.id=a.USER_ID and a.COMPANY_ID=c.COMPANY_ID and a.COMPANY_ID=$cbo_company_name and c.status_active=1 and c.is_deleted=0 $approved_cond group by a.id,a.USER_ID,a.APPROVED_BY,a.COMPANY_ID,a.APPROVED_DATE,c.COMPANY_ID,c.is_approved,b.id,b.id,b.USER_NAME,b.USER_FULL_NAME";

	//echo $sql;die();

	$dataArr=sql_select($sql);
	
  ?> 
 
  <div  id="data_panel" align="center" style="width:100%">
     <fieldset style="width: 100%">
        <table width="100%" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
       <thead>
				<tr>
					<th width="80">Sl</th>
					<th width="80">Approval Person</th>
					<th width="120">Approval Status</th>
					<th width="100" >Approval Date</th>
					<th width="120">Approval Time</th>
					
				</tr>
           
      </thead> 
       <?
		
		$i=1;	  
		foreach ($dataArr as $row) 
		{  
	   ?> 
	   <tr  bgColor="<?= $bgcolor;?>">
		<td><?=$i;?></td> 
		<td><? echo $row[csf('USER_FULL_NAME')];?>(<? echo $row[csf('USER_NAME')];?>)</td>
		<td><? echo $Approval_status[$row[csf('is_approved')]];?></td>
		<td style="text-align: right;"><? echo $row[csf('APPROVED_DATE')];?></td> 
		<td style="text-align: right;"></td> 
		
	   </tr>
		<?
		$i++;
	
		}
		
		?>
     
        </table>

      </fieldset>

  </div>
     

    <?
    exit(); 
  
}

if($action=="partial_popup_________________off")
{   extract($_REQUEST);
	echo load_html_head_contents($tittle." Info", "../../../", 1, 1,'','','');	
    //echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    //extract($_REQUEST);
    //$key=$row['wo_pi_no'].'**'.$row['item_description'].'**'.$row['order_uom'];
   
	// $sql="SELECT a.id as requisition_id, a.requ_no, a.requ_prefix_num, a.company_id, a.requisition_date, a.is_approved, a.insert_date, a.req_by, a.store_name, sum(b.amount) as amount from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $requistion_cond  $date_cond $approved_cond  group by a.id, a.company_id,  a.requisition_date, a.is_approved, a.insert_date,a.req_by, a.requ_no, a.requ_prefix_num, a.store_name  order by a.id desc";
	$Approval_status = array(0 => "pending",1 => "Approved", 2 => "Un_approved", 3 => "Partial Approved");
   
	$approved_cond=" and c.is_approved=3";
	$sql="select a.id,a.USER_ID,a.APPROVED_BY,a.COMPANY_ID,a.APPROVED_DATE,c.COMPANY_ID,c.is_approved,b.id,b.id,b.USER_NAME,b.USER_FULL_NAME from user_passwd b ,electronic_approval_setup a,inv_purchase_requisition_mst c where b.id=a.USER_ID and a.COMPANY_ID=c.COMPANY_ID and a.COMPANY_ID=$cbo_company_name and c.status_active=1 and c.is_deleted=0 $approved_cond group by a.id,a.USER_ID,a.APPROVED_BY,a.COMPANY_ID,a.APPROVED_DATE,c.COMPANY_ID,c.is_approved,b.id,b.id,b.USER_NAME,b.USER_FULL_NAME";

	//echo $sql;die();

	$dataArr=sql_select($sql);
	
  ?> 
  
  <div  id="data_panel" align="center" style="width:100%">
    <fieldset style="width: 100%">
      <table width="100%" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
		<thead>
				<tr>
					<th width="80">Sl</th>
					<th width="80">Approval Person</th>
					<th width="120">Approval Status</th>
					<th width="100" >Approval Date</th>
					<th width="120">Approval Time</th>
					
				</tr>
			
		</thead> 
       <?
		
		$i=1;	  
		foreach ($dataArr as $row) 
		{  
	   ?> 
	   <tr  bgColor="<?= $bgcolor;?>">
		<td><?=$i;?></td> 
		<td><? echo $row[csf('USER_FULL_NAME')];?>(<? echo $row[csf('USER_NAME')];?>)</td>
		<td><? echo $Approval_status[$row[csf('is_approved')]];?></td>
		<td style="text-align: right;"><? echo $row[csf('APPROVED_DATE')];?></td> 
		<td style="text-align: right;"></td> 
		
	   </tr>
		<?
		$i++;
	
		}
		
		?>
     
      </table>

    </fieldset>

     
  </div>						
   
    <?
    exit(); 
 
}

?>