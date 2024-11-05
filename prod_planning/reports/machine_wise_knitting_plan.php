<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Machine Wise Knitting Plan.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	11-05-2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Machine Wise Knitting Plan", "../../", 1, 1,'',1,1);

?>
	
<script src="plan_board.js" type="text/javascript"></script>
<script src="contextmenu.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="plan_board.css" >

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
	if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
	{
		return;
	}
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_floor_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/machine_wise_knitting_plan_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
	
function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container').html(response[0]);
		
		/*var tot_date=response[2];
		
		var tdid=new Array();
		var td_col=new Array();
		var td_op=new Array();
		var td_method=new Array();
		var col_id_r=5;
		
		tdid.push('value_capacity');
		td_col.push(col_id_r); 
		td_op.push("sum");
		td_method.push("innerHTML");
		
		for(var i=1; i<=tot_date; i++)
		{
			col_id_r=col_id_r+1; td_col.push(col_id_r);

			tdid.push('value_qnty_'+i);
			td_op.push("sum");
			td_method.push("innerHTML");
		}

		var tableFilters = { 
			//col_0: "none" 
			col_operation: {
							   id: tdid,
							   col: td_col,
							   operation: td_op,
							   write_method: td_method
							}
		}*/
		//setFilterGrid("tbl_list_search",-1,tableFilters);
		//append_report_checkbox('table_header_1',1);
		// $("input:checkbox").hide();
		show_msg('3');
		release_freezing();
 	}
}

function openmypage(program_id)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/machine_wise_knitting_plan_controller.php?program_id='+program_id+'&action=plan_deails', 'Detail Veiw', 'width=600px, height=410px,center=1,resize=0,scrolling=0','../');
}

</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="knittingPlanChartReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:700px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:700px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Production Floor</th>
                    <th class="must_entry_caption">Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingPlanChartReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                </thead>
                <tbody>
                    <tr align="center">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/machine_wise_knitting_plan_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
                            ?>
                        </td>
                        <td id="floor_td">
							<? echo create_drop_down( "cbo_floor_id", 160, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo "24-05-2016";//date("d-m-Y"); ?>" class="datepicker" style="width:70px" readonly/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo "31-05-2016";// date('d-m-Y', strtotime("+14 days", strtotime(date("d-m-Y")))); ?>" class="datepicker" style="width:70px" readonly/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
 </form>   
</body>
<ul id="myMenu" class="contextMenu">
    <li class="new"><a href="#new">New Plan</a></li>
    <li class="cut separator"><a href="#cut">Cut From Here</a></li>
    <li class="move"><a href="#move">Move Plan</a></li>
    <li class="forwards"><a href="#forwards">Forward by Days</a></li>
    <li class="re_plan"><a href="#re_plan">Revise Plan</a></li>
    <!-- <li class="re_plan_bfore_prod"><a href="#re_plan_bfore_prod">Revise Plan (Before Production)  (After Production) </a></li> -->
    
    <li class="paste separator"><a href="#paste">Paste Here</a></li>
    <!--<li class="undo"><a href="#undo">Undo</a></li>-->
    <li class="delete"><a href="#delete">Delete Plan</a></li>
    
    <!--<li class="show_info separator"><a href="#show_info">Show Current Status</a></li>-->
    <li class="save separator"><a href="#save">Save Board</a></li>
    <li class="cancel"><a href="#cancel">Cancel</a></li>
    <li class="quit separator"><a href="#quit">Quit</a></li>
</ul>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>

	var last_operation='';
	var forwd_days=0;
	function showmenu( idd )
	{
		$("#"+idd).contextMenu({
			menu: 'myMenu'
		},
		function( action, el, pos ) 
		{ 
			var pgroup=$('#'+ $(el).attr('id')).attr( 'plan_group' );
			//alert(action);return;
			
			if( action=='new')
			{
				if( (pgroup*1)!=0 )
				{ 
					alert('Please Click on Blank space for New Plan.'); 
					return;
				}
				leventType='new';
				last_operation='new';//alert($(el).attr('id'));return;
				context_menu_operation( $(el).attr('id'), action );
			}
			else if( action=='cut')
			{
				if($('#'+ $(el).attr('id')).attr( 'isnew' )!=0)
				{
					//alert('You can not edit New Plan'); 
					//return;
				}
				
				if( (pgroup*1)==0 )
				{ 
					alert('Please Click on a plan cut or split.'); 
					return;
				}
				
				last_operation='cut';
				leventType='cut';
				event_check( $(el).attr('id'));
			}
			else if( action=='forwards')
			{
				leventType='forwards';
				var days = prompt("Please enter Days", "Forward Days");
				if ( days == null ) {
					alert('Operation Cancelled.');
					return;
				}
				
				last_operation='forwards';
				forwd_days=days;
				event_check_copy_forward( $(el).attr('id'), days );
				//event_check_copy( $(el).attr('id') );
			}
			else if( action=='paste')
			{
				if( (pgroup*1)!=0 )
				{ 
					alert('Please Click on Blank space to paste copied plan.'); 
					return;
				}
				last_operation='paste';
				paste_plan( $(el).attr('id') );
			}
			else if( action=='move') //ok
			{
				if($('#'+ $(el).attr('id')).attr( 'isnew' )!=0)
				{
					alert('You can not Move/Edit New Plan.'); 
					return;
				}
				
				leventType='move';
				if( (pgroup*1)==0 )
				{ 
					alert('Please Click on a plan to Move.'); 
					return;
				}
				last_operation='move';
				event_check_copy( $(el).attr('id') );
			}
			else if( action=='delete' )
			{
				if( (pgroup*1)==0 )
				{ 
					alert('Please Click on a plan to Delete.'); 
					return;
				}
				
				var r = confirm("You are trying to delete a plan! Are you sure to delete?");
				if (r == true) {
					last_operation='delete';
					leventType='delete';
					delete_full_plan( $(el).attr('id') );
				}
			}
			else if( action=='undo' )
			{
				 undo_event();
				 last_operation='undo';
			}
			else if( action=='re_plan_bfore_prod')
			{
				var plan_qnty= $('#'+$(el).attr('id')).attr('plan_qnty');
				var compid= $('#'+$(el).attr('id')).attr('compid');
				var comptarg= $('#'+$(el).attr('id')).attr('comptarg');
				var compstart= $('#'+$(el).attr('id')).attr('compstart');
				var off_day_plan= $('#'+$(el).attr('id')).attr('off_day_plan');
				var duration= Number($('#'+$(el).attr('id')).attr('duration'));
				var plan_start=$('#'+$(el).attr('id')).attr('start_date');
				var plan_group=$('#'+$(el).attr('id')).attr('plan_group');
				var compinc=$('#'+$(el).attr('id')).attr('compinc');
				
				var line=$(el).attr('id').split("-");
				var edtstartdate=line[2].substr(0, 2)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(4,4)
				var actplanenddate=js_date_add( line[2].substr(4, 4)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(0,2), -1 );
				
				var did=plan_start.split("-");
				var ymd=did[2]+"-"+did[1]+"-"+did[0]; 
				var mn=0;
				var mnn=0;
				var tot_prod_qny=0;
				var rem_plan_qny=0;
				var off_days_avlble='';
				var del_dur=0;
				for( var i=0; i<duration; i++ )
				{
					var cldte=js_date_add( ymd, i )
					var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
					
					if( $('#tdbody-'+ line[1]+"-"+clkdte).hasClass( "verticalStripes_off" ).toString()=='true' )
					{
						mn++;
						if(off_days_avlble=='') off_days_avlble=cldte; else off_days_avlble= off_days_avlble+","+cldte;
					}
					else
						mnn++;	
						
					if( line[2] >= clkdte )
					{
						tot_prod_qny+=($('#tdbody-'+ line[1]+"-"+clkdte).attr('today_production')*1);
						
					}
					else
					{
						if( plan_group==$('#tdbody-'+ line[1]+"-"+clkdte).attr('plan_group'))
							del_dur++;
					}
					
				}
				if( del_dur>0 ) del_dur++;
				rem_plan_qny=plan_qnty-tot_prod_qny;
				var oldDur=duration-del_dur;
				 
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/planning_board_controller.php?action=revise_planning&plan_qnty='+plan_qnty+'&compid='+compid+'&off_days_list='+off_days_avlble+'&off_days_count='+mn+'&comptarg='+comptarg+'&compstart='+compstart+'&compinc='+compinc+'&off_day_plan='+off_day_plan+'&rem_plan_qny='+rem_plan_qny+'&tot_prod_qny='+tot_prod_qny+'&company_id='+$('#cbo_company_mst').val()+'&location_id='+$('#cbo_location_name').val()+'&floor_id='+$('#cbo_floor_name').val()+'&line_id='+line[1]+'&line_id='+line[1]+'&edtstartdate='+edtstartdate, '', 'width=600px,height=300px, center=1,resize=0,scrolling=0','')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var theemail=this.contentDoc.getElementById("selected_job");
					if (theemail.value!="")
					{
						var sel_data=theemail.value.split("__");
						
						var plength=Number( get_production_days( sel_data[3]+"**"+sel_data[4]+"**"+sel_data[5]+"**"+sel_data[6],sel_data[1], edtstartdate, sel_data[2] ,sel_data[8] ));
						if((plength*1)<1) { alert('Please select different date.'); return; }
						
						var lastdate=js_date_add( line[2].substr(4, 4)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(0,2), plength );
						
						var podtl=$('#'+$(el).attr('id')).attr('podtls').split("**");
						
						k=(Array.max(pln_grp_indx))+1;
						pln_grp_indx.push(k);
						
					 var vtitle='Start Date:: '+podtl[15]+', End Date:: '+actplanenddate+', Quantity:: '+((plan_qnty*1)-(rem_plan_qny*1))+', Item:: '+item_name[podtl[19]]+', Order:: '+podtl[17];
					var newpodtls=sel_data[8]+"**"+sel_data[8]+"**"+podtl[2]+"**"+podtl[0]+"**"+actplanenddate+"**0**"+lastdate+"**0**8**"+sel_data[1]+"**"+sel_data[3]+"**"+sel_data[4]+"**"+sel_data[5]+"**"+sel_data[6]+"**0**"+edtstartdate+"**"+lastdate+"**"+podtl[17]+"**"+day_qnty+"**"+podtl[19]+"**"+podtl[20]+"**"+podtl[21]+"**"+podtl[22]+"**"+podtl[23];
						
						for(var nm=0; nm<=duration; nm++)
						{
							var cldte=js_date_add( ymd, nm )
							var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
							
							if( line[2] <= clkdte ) 
								clear_td( 'tdbody-'+ line[1]+"-"+clkdte, '', 1, 0 )
							else
							{
								$('#tdbody-'+line[1]+"-"+clkdte).attr('isnew','0'); 
								$('#tdbody-'+line[1]+"-"+clkdte).attr('isedited','1');
								$('#tdbody-'+line[1]+"-"+clkdte).attr('duration', oldDur );
								$('#tdbody-'+line[1]+"-"+clkdte).attr('podtls', newpodtls);
								$('#tdbody-'+line[1]+"-"+clkdte).attr('plan_qnty',((plan_qnty*1)-(rem_plan_qny*1)));
								$('#tdbody-'+line[1]+"-"+clkdte).attr('plan_end_date', actplanenddate );
								$('#tdbody-'+line[1]+"-"+clkdte).attr('title',vtitle);  
							}
						}
						
						var newpodtls="0**"+sel_data[8]+"**"+podtl[2]+"**"+podtl[0]+"**"+edtstartdate+"**0**"+lastdate+"**0**8**"+sel_data[1]+"**"+sel_data[3]+"**"+sel_data[4]+"**"+sel_data[5]+"**"+sel_data[6]+"**0**"+edtstartdate+"**"+lastdate+"**"+podtl[17]+"**"+day_qnty+"**"+podtl[19]+"**"+podtl[20]+"**"+podtl[21]+"**"+podtl[22]+"**"+podtl[23];
						
						draw_chart( sel_data[8], edtstartdate, plength, newpodtls, sel_data[1], sel_data[0] , 0 )
						
					}
				}
				
				leventType='re_plan';
				last_operation='re_plan';
				
			}
			else if( action=='re_plan' )
			{
				var plan_qnty= $('#'+$(el).attr('id')).attr('plan_qnty');
				var compid= $('#'+$(el).attr('id')).attr('compid');
				var comptarg= $('#'+$(el).attr('id')).attr('comptarg');
				var compstart= $('#'+$(el).attr('id')).attr('compstart');
				var off_day_plan= $('#'+$(el).attr('id')).attr('off_day_plan');
				var duration= Number($('#'+$(el).attr('id')).attr('duration'));
				var plan_start=$('#'+$(el).attr('id')).attr('start_date');
				var plan_group=$('#'+$(el).attr('id')).attr('plan_group');
				var compinc=$('#'+$(el).attr('id')).attr('compinc');
				var podtls=$('#'+$(el).attr('id')).attr('podtls');
				var podtlsarr=podtls.split("**");
				
				var line=$(el).attr('id').split("-");
				var edtstartdate=line[2].substr(0, 2)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(4,4)
				var actplanenddate=js_date_add( line[2].substr(4, 4)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(0,2), -1 );
				
				var did=plan_start.split("-");
				var ymd=did[2]+"-"+did[1]+"-"+did[0]; 
				var mn=0;
				var mnn=0;
				var tot_prod_qny=0;
				var rem_plan_qny=0;
				var off_days_avlble='';
				var del_dur=0;
				var cont=1;
				var part_qnty=0
				for( var i=0; i<duration; i++ )  // Find offdays, prod qnty, delete duration total duration
				{
					var cldte=js_date_add( ymd, i )
					var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
					
					if( cldte==edtstartdate) cont=0;
					
					if( cont==1) part_qnty+=($('#tdbody-'+ line[1]+"-"+clkdte).attr('today_plan_qnty')*1);
					 
					if( $('#tdbody-'+ line[1]+"-"+clkdte ).hasClass( "verticalStripes_off" ).toString()=='true' )
					{
						mn++;
						if( off_days_avlble=='') off_days_avlble=cldte; else off_days_avlble= off_days_avlble+","+cldte;
					}
					else
						mnn++;
					
					if( line[2] >= clkdte )  //Why??
					{
						tot_prod_qny+=($('#tdbody-'+ line[1]+"-"+clkdte).attr('today_production')*1);
					}
					else
					{
						if( plan_group==$('#tdbody-'+ line[1]+"-"+clkdte).attr('plan_group') )
							del_dur++;
					}
					
				}
				
				if( del_dur>0 ) del_dur++;
				rem_plan_qny=plan_qnty-tot_prod_qny;
				var oldDur=duration-del_dur;
				//alert(part_qnty)
				if( podtlsarr[28] != 'undefined' )
				{
					if( part_qnty!=0 )part_qnty =(podtlsarr[28]*1)+part_qnty;
				}
				// alert(rem_plan_qny+'='+ podtlsarr[28]+'='+tot_prod_qny+'='+part_qnty +'='+plan_qnty)
				//2300==0=1000=2300
				
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/planning_board_controller.php?action=revise_planning&plan_qnty='+plan_qnty+'&compid='+compid+'&off_days_list='+off_days_avlble+'&off_days_count='+mn+'&comptarg='+comptarg+'&compstart='+compstart+'&compinc='+compinc+'&off_day_plan='+off_day_plan+'&rem_plan_qny='+rem_plan_qny+'&tot_prod_qny='+tot_prod_qny+'&company_id='+$('#cbo_company_mst').val()+'&location_id='+$('#cbo_location_name').val()+'&floor_id='+$('#cbo_floor_name').val()+'&line_id='+line[1]+'&line_id='+line[1]+'&edtstartdate='+edtstartdate+'&part_qnty='+part_qnty  , '', 'width=600px,height=300px, center=1,resize=0,scrolling=0','')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var theemail=this.contentDoc.getElementById("selected_job");
					if (theemail.value!="")
					{
							var sel_data=theemail.value.split("__");
						 
							var compx= sel_data[3]+"**"+sel_data[4] +"**"+sel_data[5]+"**"+sel_data[6]+"**"+sel_data[1]+"**"+sel_data[1]; 
							leventType='forwards';
							//	Number(get_production_days( undo_event_start_complx, undo_event_start_qnty, undo_event_start_date ,undo_event_start_off));
							var plength=Number( get_production_days( compx,sel_data[1], edtstartdate, sel_data[2] ,sel_data[8] ));
							leventType='re_plan';
							
							if((plength*1)<1) { alert('Please select different date.'); return; }
							
							var lastdate=js_date_add( line[2].substr(4, 4)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(0,2), plength );
							
							var podtl=$('#'+$(el).attr('id')).attr('podtls').split("**");
							
							k=(Array.max(pln_grp_indx))+1;
							pln_grp_indx.push(k);
							
							var vtitle='Start Date:: '+podtl[15]+', End Date:: '+actplanenddate+', Quantity:: '+((plan_qnty*1)-(rem_plan_qny*1))+', Item:: '+item_name[podtl[19]]+', Order:: '+podtl[17];
							var newpodtls=sel_data[8]+"**"+sel_data[8]+"**"+podtl[2]+"**"+podtl[0]+"**"+actplanenddate+"**0**"+lastdate+"**0**8**"+sel_data[1]+"**"+sel_data[3]+"**"+sel_data[4]+"**"+sel_data[5]+"**"+sel_data[6]+"**0**"+edtstartdate+"**"+lastdate+"**"+podtl[17]+"**"+day_qnty+"**"+podtl[19]+"**"+podtl[20]+"**"+podtl[21]+"**"+podtl[22]+"**"+podtl[23]+"**"+podtl[24]+"**"+podtl[25]+"**"+podtl[26]+"**"+podtl[27];
							
							for(var nm=0; nm<=duration; nm++)
							{
								var cldte=js_date_add( ymd, nm )
								var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
								
								if( line[2] <= clkdte ) 
									clear_td( 'tdbody-'+ line[1]+"-"+clkdte, '', 1, 0 )
								else
								{
									$('#tdbody-'+line[1]+"-"+clkdte).attr('isnew','0'); 
									$('#tdbody-'+line[1]+"-"+clkdte).attr('isedited','1');
									$('#tdbody-'+line[1]+"-"+clkdte).attr('duration', oldDur );
									$('#tdbody-'+line[1]+"-"+clkdte).attr('podtls', newpodtls);
									$('#tdbody-'+line[1]+"-"+clkdte).attr('plan_qnty',((plan_qnty*1)-(rem_plan_qny*1)));
									$('#tdbody-'+line[1]+"-"+clkdte).attr('plan_end_date', actplanenddate );
									$('#tdbody-'+line[1]+"-"+clkdte).attr('title',vtitle);  
								}
								//alert('#tdbody-'+line[1]+"-"+clkdte)
							}
							
							var newpodtls="0**"+sel_data[8]+"**"+podtl[2]+"**"+podtl[0]+"**"+edtstartdate+"**0**"+lastdate+"**0**8**"+sel_data[1]+"**"+sel_data[3]+"**"+sel_data[4]+"**"+sel_data[5]+"**"+sel_data[6]+"**0**"+edtstartdate+"**"+lastdate+"**"+podtl[17]+"**"+day_qnty+"**"+podtl[19]+"**"+podtl[20]+"**"+podtl[21]+"**"+podtl[22]+"**"+podtl[23]+"**"+podtl[24]+"**"+podtl[25]+"**"+podtl[26]+"**"+podtl[27];
							
							draw_chart( sel_data[8], edtstartdate, plength, newpodtls, sel_data[1], sel_data[0] , 0 )
						}
						
					 
				}
				
				leventType='re_plan';
				last_operation='re_plan';
			}
			else if( action=='save')
			{
				
				fnc_save_planning();
				//alert($(el).attr('id'))
				last_operation='save';
			}
			else if( action=="cancel")
			{
				if( last_operation=='cut' || last_operation=='move')
				{
					if(leventstart=='') return;
					
					var pln_grp=$('#'+leventstart).attr('plan_group');
					var duration= ($('#'+leventstart).attr('duration'));
					duration=duration.split(".");
					var clkdt=leventstart.split("-");
					for( var i=0; i<=copy_plan_length; i++ )
					{
						var ymd=clkdt[2].substr(4,4)+"-"+clkdt[2].substr(2,2)+"-"+clkdt[2].substr(0,2);
						var cldte=js_date_add( ymd, i )
						var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
						
						if(pln_grp==$('#tdbody-'+ clkdt[1]+"-"+clkdte).attr('plan_group'))
						{
							//$('#tdbody-'+ line[1]+"-"+clkdte).css('border-color','#F2B7E2' );
							 $('#tdbody-'+ clkdt[1]+"-"+clkdte).css('border-color','#F5F5F5' );
							//$('#tdbody-'+ clkdt[1]+"-"+clkdte).css('background', '#0AE7E1');
						}
						else  break ;
					}
				}
				leventstart='';
				last_operation='cancel';
			}
		});
	}

</script>
</html>
