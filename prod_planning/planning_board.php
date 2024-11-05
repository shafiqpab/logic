<?php
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract( $_REQUEST );

$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Planning Board","../", 1, 1, $unicode,'','');
$cdate=date("dmY",time());

?>

<script>
	var today_sys_date='<? echo date("Ymd", time() ); ?>';
		var complexity_color_code=new Array (5);
		complexity_color_code[0]="#FF9900";  //yellowish
		complexity_color_code[1]="#FF00FF";	 //pink
		complexity_color_code[2]="#00FF00";  //Green 00FF00  
		complexity_color_code[3]="#0000FF";  //Blue
		complexity_color_code[4]="#FF0000";  //Blue
		
		//array( 0=>"",1=>"Basic",2=>"Fancy", 3=>"Critical", 4=>"Average" );

function showRightaBar( plan_uses )
{
	$( "#right_side_bar" ).toggle( "slow", function(   ) {
		if(  plan_uses!=1 )
		{
			var htm=' <input type="button" class="formbutton" style="width:120px;" onClick="fnc_save_planning()" value="Save Board"><a href="##" onclick="showRightaBar(0)" style="margin-left:20px;text-decoration:none">X</a>'+
        			'<input type="button" class="formbutton" style="width:120px; margin-top:3px" onClick="fnc_release_board()" value="Free Board"><a href="##" onclick="showRightaBar(0)" style="margin-left:22px;text-decoration:none">&nbsp;&nbsp;</a>'+
        			'<table width="150" cellpadding="0" cellspacing="0"   class="table_legend" >'+
					'<thead><tr>'+
					'	<th width="50"> Color</th><th> Color Meaning</th>'+
					'</tr></thead>'+	
					'<tr>'+
					'	<td height="15" width="50" bgcolor="#FF9900"></td><td>No Complexity</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#FF00FF"> </td><td>Basic Compl.</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#00FF00"> </td><td>Fancy Compl.</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#0000FF"> </td><td>Critical Compl.</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#FF0000"> </td><td>Average Compl.</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#F2B7E2"> </td><td>Selected Plan</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="6" bgcolor="#CCCCCC" colspan="2"></td>'+
					'</tr>'+
					'<tr>'+
					'	<td height="15" width="50" bgcolor="#73CAD5"></td><td>Fresh Plan</td>'+
					'</tr>'  + 
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#FFFFA8"> </td><td>Offday Board</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#9C8AE3"> </td><td>Crossed plan</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#C6C600"> </td><td>Production</td>'+
					'</tr>'+
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#FF0000"> </td><td>TNA Crossed</td>'+
					'</tr>'+  
					'<tr>'+
					' 	<td height="15"  width="50" bgcolor="#909553"> </td><td>No Production</td>'+
					'</tr>'+ 
					  
				'</table>';	
				
		  	$( "#right_side_bar" ).html(htm);
         }
	});
}

</script>

<script src="plan_board.js" type="text/javascript"></script>
<script src="contextmenu.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="plan_board.css" >

</head>

<body>

<span id="selection_line_vert"></span>
<span id="selection_line_hor"></span>
<span id="scroll_contents" ></span>
 
<center>
	<div style="width:100%;" align="center">
<? //echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1060px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel"> 
            <fieldset style="width:1060px;">
            	<table width="1050"  class="rpt_table" rules="all" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="100"  onClick="showRightaBar('0')" align="right">Company Name  
                        <input type="hidden" id="txt_cdate">
                        </td>
                        <td width="150" >
                            <? 
                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3)  $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'requires/planning_board_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                        <td width="100" align="right">Location Name</td>
                        <td id="location_td" width="150">
                            <?  echo create_drop_down( "cbo_location_name", 150, $blank_array,"",1, "-- Select Location --",1 ); ?>
                        </td>
                        <td width="70" align="right">Floor</td>
                        <td id="floor_td" width="150">
                            <?  echo create_drop_down( "cbo_floor_name", 150, $blank_array,"",1, "-- Select Floor --",1 ); ?>
                        </td>
                        <td width="100" align="right">Plan Start Date</td>
                        <td>
                            <input type="text" class="datepicker" value="<? echo date("d-m-Y",time()); ?>" readonly style="width:80px" id="txt_start_date">
                        </td>
                        <td height="35" valign="top">
                            <input type="button" name="generate" class="formbutton" style="width:100px" value="Generate" onClick="populate_line()" >
                        </td>
                        <td height="35" valign="middle">
                            <input type="hidden" class="text_boxes" value=""   style="width:100px;" title="Double Click to Search" onDblClick="search_element()" id="txt_search_text">
                            
                             <input type="hidden" class="text_boxes"   style="width:100px;" id="deleted_id">
                             <input type="hidden" class="text_boxes"   style="width:100px;" id="last_event_id">
                             <input type="hidden" class="text_boxes"   style="width:100px;" id="deleted_info">
                             <input type="hidden" class="text_boxes"   style="width:100px;" id="operation_closed">
                             <input type="hidden" class="text_boxes"   style="width:100px;" value="<? echo $_SESSION['menu_id']; ?>" id="current_menu">
                        </td>
                    </tr>
                </table>
            </fieldset>
         </div>
    </div>
       
<div id="right_side_bar" style=" position:fixed; opacity:0.9; z-index:950; width:150px; height:auto; background-color:#DEE9D8; left: 5px; top:50px; border:1px solid #FF0000">
 		     
</div>
<span id="plan_container"></span>
 
<table width="100%">
	<tr>
    	<td width="100%" align="center" valign="bottom">
        	<div id="footer" style=" position:fixed; opacity:1.8; z-index:900; width:1150px; height:200px; background-color:#DEE9D8; bottom:30px; ">
            
            </div> 
        </td>
    </tr>
</table>

 <ul id="myMenu" class="contextMenu">
			<li class="new"><a href="#new">New Plan</a></li>
			<li class="cut separator"><a href="#cut">Cut From Here</a></li>
			<li class="move"><a href="#move">Move Plan</a></li>
            <li class="forwards"><a href="#forwards">Forward by Days</a></li>
            <li class="re_plan"><a href="#re_plan">Revise Plan</a></li>
            <!-- <li class="re_plan_bfore_prod"><a href="#re_plan_bfore_prod">Revise Plan (Before Production)  (After Production) </a></li> -->
            
			<li class="paste separator"><a href="#paste">Paste Here</a></li>
            <li class="undo"><a href="#undo">Undo</a></li>
			<li class="delete"><a href="#delete">Delete Plan</a></li>
			
            <li class="show_info separator"><a href="#show_info">Show Current Status</a></li>
            <li class="save separator"><a href="#save">Save Board</a></li>
            <li class="cancel"><a href="#cancel">Cancel</a></li>
            <li class="quit separator"><a href="#quit">Quit</a></li>
		</ul>
 </center>

</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//id="common"
$.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}
var gblcount=0;
var gblsearch=Array();

function search_element()
{
	//event.preventDefault();
	$('td[name="'+ $('#txt_search_text').val()+'"]').each(function() {
		if( $(this).hasClass( "verticalStripes_off" ).toString()=='true' )
			$(this).css('border-bottom-color', '#FFFFA8');
		else
			$(this).css('border-color','#F5F5F5' );
	});
	
	var tname= $('#txt_search_text').val();
	$('td[name="'+tname+'"]').attr('bgcolor','FF3366');
	
	$('td[name="'+tname+'"]').each(function() {
			$(this).css('border-color','#F2B7E2' );
	});
	
	var myList=document.getElementsByName( $('#txt_search_text').val())
	for(var k=0; k<myList.length; k++)
	{
		if(!gblsearch[$('#txt_search_text').val()] )
		{
			 gblsearch[$('#txt_search_text').val()]=1;
			 document.getElementById(myList[k].id).scrollIntoView();
			 return;
		}
		else if(gblsearch[$('#txt_search_text').val()]=="" || gblsearch[$('#txt_search_text').val()]==0)
		{ 
			gblsearch.length=0;
			gblsearch[$('#txt_search_text').val()]=1;
			document.getElementById(myList[k].id).scrollIntoView();
			return;
		}
		else
		{
			gblsearch[$('#txt_search_text').val()]=((gblsearch[$('#txt_search_text').val()]*1)+1)-1;
			if( gblsearch[$('#txt_search_text').val()] == myList.length )
			{
				k=gblsearch[$('#txt_search_text').val()]-1;
				gblsearch[$('#txt_search_text').val()]=0;
			}
			else k=gblsearch[$('#txt_search_text').val()];
			
			document.getElementById(myList[k].id).scrollIntoView();
			return;
		}
	}
}

function scrollIntoView(element, container) {
  var containerTop = $(container).scrollTop(); 
  var containerBottom = containerTop + $(container).height(); 
  var elemTop = element.offsetTop;
  var elemBottom = elemTop + $(element).height(); 
  if (elemTop < containerTop) {
    $(container).scrollTop(elemTop);
  } else if (elemBottom > containerBottom) {
    $(container).scrollTop(elemBottom - $(container).height());
  }
}

<?php

$buyer_name=return_library_array( "select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and a.status_active=1 and a.is_deleted=0", "id", "buyer_name"  );

$buyer_name= json_encode( $buyer_name );
echo "var buyer_name = ". $buyer_name . ";\n";

$item_name= json_encode( $garments_item );
echo "var item_name = ". $item_name . ";\n";

$line_name=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
$line_name= json_encode( $line_name );
echo "var line_arr = ". $line_name . ";\n";

?>
var last_operation='';
var forwd_days=0;
function showmenu( idd )
{
	$("#"+idd).contextMenu({
				menu: 'myMenu'
			},
			function( action, el, pos ) {
				var pgroup=$('#'+ $(el).attr('id')).attr( 'plan_group' );
				 
				if( action!='show_info' )
				{
					if( $( '#'+ $(el).attr('id') ).hasClass( "verticalStripes1_crossed" ).toString()=='true' )
					{
						alert('Sorry! This is a partial Plan. You cannot edit this plan.');
						return;
					}
					if( $( '#'+ $(el).attr('id') ).hasClass( "verticalStripes1_produced" ).toString()=='true' )
					{
						alert('Sorry! Production has been started. You cannot edit this plan.');
						return;
					}
					var lines=$(el).attr('id').split("-");
					var seldate=lines[2].substr(4, 4)+""+lines[2].substr(2, 2)+""+lines[2].substr(0,2)
					//alert(today_sys_date+"=="+seldate)
					if( (today_sys_date*1)>(seldate*1) )
					{
						//alert( 'Sorry! This is a old plan. You cannot edit this plan.' );
						//return;
					}
				}
				
				if( $('#operation_closed').val()!='')
				{
					alert('This board was Saved. Please Re-Generate  again before any Operation.');
					return;
				}
				if( $('#txt_lock_operation').val()!=0)
				{
					alert('This board Locked by another user. Please wait until released.');
					return;
				}
				
				if( action=='new')
				{
					if( (pgroup*1)!=0 )
					{ 
						alert('Please Click on Blank space for New Plan.'); 
						return;
					}
					leventType='new';
					last_operation='new';
					//last_operation='new';
					context_menu_operation( $(el).attr('id'), action )
					
				}
				
				if( action=='cut')
				{
					var did=$('#'+ $(el).attr('id')).attr( 'start_date' ).split("-");
					var ymd=did[2]+"-"+did[1]+"-"+did[0]; 
					var line=$(el).attr('id').split("-");
					 
					var ddmmyy=did[0]+""+did[1]+""+did[2];
					if(ddmmyy==line[2])
					{
						alert( 'Cut Operation is not allowed for Complete Plan. Please use Revise Instead.'); 
						return;
					}
					 
					
					
					if($('#'+ $(el).attr('id')).attr( 'isnew' )!=0)
					{
						//alert('You can not edit New Plan'); 
						//return;
					}
					if($('#'+ $(el).attr('id')).attr( 'name' )=="Multiple PO")
					{
						alert( 'Cut Operation id not allowed for Group PO Plan. Please use Revise Plan instead.'); 
						return;
					}
					if( (pgroup*1)==0 )
					{ 
						alert('Please Click on a plan cut or split.'); 
						return;
					}
					last_operation='cut';
					leventType='cut';
					event_check( $(el).attr('id') );
				}
				if( action=='forwards')
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
				if( action=='paste')
				{
					if( (pgroup*1)!=0 )
					{ 
						alert('Please Click on Blank space to paste copied plan.'); 
						return;
					}
					last_operation='paste';
					paste_plan( $(el).attr('id') );
				}
				if( action=='move') //ok
				{
					
					if($('#'+ $(el).attr('id')).attr( 'isnew' )!=0)
					{
						//alert('You can not Move/edit New Plan. PLease Undo.'); 
						//return;
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
				if( action=='delete' )
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
				if( action=='undo' )
				{
					 undo_event();
					 last_operation='undo';
				}
				if( action=='re_plan' )
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
					var group_po_info=$('#'+$(el).attr('id')).attr('group_po_info');
					var line=$(el).attr('id').split("-");
					var edtstartdate=line[2].substr(0, 2)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(4,4)
					var actplanenddate=js_date_add( line[2].substr(4, 4)+"-"+line[2].substr(2, 2)+"-"+line[2].substr(0,2), -1 );
					
					var did=plan_start.split("-");
					var ymd=did[2]+"-"+did[1]+"-"+did[0]; 
					
					if($('#'+ $(el).attr('id')).attr( 'name' )=="Multiple PO")
					{
						var ddmmyy=did[0]+""+did[1]+""+did[2];
						if(ddmmyy!=line[2])
						{
							alert( 'Revise Operation is not allowed in middle for Group PO Plan. Please Revise from First Day.'); 
							return;
						}
					}
					
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
				//	alert(plan_group)
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
					
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/planning_board_controller.php?action=revise_planning&plan_qnty='+plan_qnty+'&compid='+compid+'&off_days_list='+off_days_avlble+'&off_days_count='+mn+'&comptarg='+comptarg+'&compstart='+compstart+'&compinc='+compinc+'&off_day_plan='+off_day_plan+'&rem_plan_qny='+rem_plan_qny+'&tot_prod_qny='+tot_prod_qny+'&company_id='+$('#cbo_company_mst').val()+'&location_id='+$('#cbo_location_name').val()+'&floor_id='+$('#cbo_floor_name').val()+'&line_id='+line[1]+'&line_id='+line[1]+'&edtstartdate='+edtstartdate+'&part_qnty='+part_qnty+'&group_po_info='+group_po_info , '', 'width=750px,height=350px, center=1,resize=0,scrolling=0','')
					emailwindow.onclose=function()
					{
						var theform=this.contentDoc.forms[0];
						var theemail=this.contentDoc.getElementById("selected_job");
						
						if (theemail.value!="")
						{
								var sel_data=theemail.value.split("__");
							 	
								//alert(theemail.value);
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
								
								
								
								
								//var group_po_info=$('#'+$(el).attr('id')).attr('group_po_info');
								
								//OG-16-00640_9999_3020_21-11-2016_4600166575_21-11-2016_838384_5_2_3020_03-11-2016_19-11-2016__0_BIZZBEE
								var oldqn=(plan_qnty*1)-(sel_data[1]*1);
								var grouo_po=2;
								var npos=group_po_info.split("|*|");
								if( npos.length>1 )
								{
									grouo_po=1;
									podtl[17]="Multiple PO";
									group_po_info=sel_data[9];
								}
								else
								{
									var tmpd=group_po_info.split("_");
									tmpd[9]=oldqn;//((plan_qnty*1)-(rem_plan_qny*1));
									var tmp_name = tmpd.join("_");
									//group_po_info=group_po_info.split('_');
									
									var tmpd1=group_po_info.split("_");
									tmpd1[9]=oldqn;
									group_po_info= tmpd1.join("_") ;
								}
								//  alert(((plan_qnty*1)+"="+(sel_data[1]*1)))
								 
								var shpdt=podtl[24].substr(6, 2)+"-"+podtl[24].substr(4, 2)+"-"+podtl[24].substr(0,4);
								if(podtl[17]!='Multiple PO')
									var vtitle='Start Date:: '+podtl[15]+', End Date:: '+actplanenddate+', Quantity:: '+oldqn+', Item:: '+item_name[podtl[19]]+', Order:: '+podtl[17]+', Ship Date:: '+shpdt+', SMV:: '+podtl[26]+', Style:: '+podtl[27];
								else
									var vtitle='Start Date:: '+podtl[15]+', End Date:: '+actplanenddate+', Quantity:: '+oldqn+', Order:: '+podtl[17];
									
								var newpodtls=sel_data[8]+"**"+sel_data[8]+"**"+podtl[2]+"**"+podtl[0]+"**"+actplanenddate+"**0**"+lastdate+"**0**8**"+oldqn+"**"+sel_data[3]+"**"+sel_data[4]+"**"+sel_data[5]+"**"+sel_data[6]+"**0**"+edtstartdate+"**"+lastdate+"**"+podtl[17]+"**"+day_qnty+"**"+podtl[19]+"**"+podtl[20]+"**"+podtl[21]+"**"+podtl[22]+"**"+podtl[23]+"**"+podtl[24]+"**"+podtl[25]+"**"+podtl[26]+"**"+podtl[27];
								
								var isedit=$('#'+$(el).attr('id')).attr('upd_id');
								for(var nm=0; nm<=duration; nm++)
								{
									var cldte=js_date_add( ymd, nm )
									var vd=cldte.replace('-', ''); var clkdte=vd.replace('-', '');
									
									if( line[2] <= clkdte ) 
										clear_td( 'tdbody-'+ line[1]+"-"+clkdte, '', 1, 0 )
									else
									{
										if( $('#tdbody-'+line[1]+"-"+clkdte).attr('plan_group')==plan_group)
										{
											// alert(((plan_qnty*1)-(rem_plan_qny*1)))
											$('#tdbody-'+line[1]+"-"+clkdte).attr('isnew','0'); 
											$('#tdbody-'+line[1]+"-"+clkdte).attr('isedited','1');
											$('#tdbody-'+line[1]+"-"+clkdte).attr('duration', oldDur );
											$('#tdbody-'+line[1]+"-"+clkdte).attr('podtls', newpodtls);
											$('#tdbody-'+line[1]+"-"+clkdte).attr('plan_qnty',oldqn);//((plan_qnty*1)-(rem_plan_qny*1)));
											$('#tdbody-'+line[1]+"-"+clkdte).attr('plan_end_date', actplanenddate );
											$('#tdbody-'+line[1]+"-"+clkdte).attr('title',vtitle); 
											$('#tdbody-'+line[1]+"-"+clkdte).attr('group_po_info',tmp_name); 
											isedit=0;
										}
									}
									//alert( '#tdbody-'+line[1]+"-"+clkdte)
								}
								if( npos.length>1 )
								{
									//grouo_po=1;
									//podtl[17]="Multiple PO";
									//group_po_info=sel_data[9];
								}
								else
								{
									var tmpd1=group_po_info.split("_");
									tmpd1[9]=sel_data[1];
									group_po_info= tmpd1.join("_");
								}
								
								var newpodtls=isedit+"**"+sel_data[8]+"**"+podtl[2]+"**"+podtl[0]+"**"+edtstartdate+"**0**"+lastdate+"**0**8**"+sel_data[1]+"**"+sel_data[3]+"**"+sel_data[4]+"**"+sel_data[5]+"**"+sel_data[6]+"**0**"+edtstartdate+"**"+lastdate+"**"+podtl[17]+"**"+day_qnty+"**"+podtl[19]+"**"+podtl[20]+"**"+podtl[21]+"**"+podtl[22]+"**"+podtl[23]+"**"+podtl[24]+"**"+podtl[25]+"**"+podtl[26]+"**"+podtl[27];
								//alert(isedit)
								
								draw_chart( sel_data[8], edtstartdate, plength, newpodtls, sel_data[1], sel_data[0] , isedit ,grouo_po,group_po_info)
							}
							
						 
					}
					//01730304108  --badhon/arif, hams it mgr.
					
					leventType='re_plan';
					last_operation='re_plan';
				}
				if( action=='show_info')
				{
					if(  $("#"+$(el).attr('id')).attr('po_id') ==undefined )
					{
						//alert('ad')
						return;
					}
					
					$( "#footer" ).toggle( "slow", function(   ) {
						show_information( $(el).attr('id') );
					});
					 
					 var wind=($(window).width()-1000)/2;
					 $('#footer').css({
						 'left' :  wind,
						'margin-left' : -wind,
					});
				}
				if( action=='save')
				{
					
					fnc_save_planning();
					//alert($(el).attr('id'))
					last_operation='save';
				}
				if( action=="cancel")
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

 