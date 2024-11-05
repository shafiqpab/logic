var db_type=0;
//var st=1; //start=1, content=2, end=3;

var st=2; //Exact=1, Starts with=2, Ends with=3,Contents=4;

/*function createObject() {
	var request_type;
	var browser = navigator.appName;
	//alert(browser);
	if( browser == "Microsoft Internet Explorer" ) {
		request_type = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		request_type = new XMLHttpRequest();
	}
	return request_type;
}*/


var field_level_data=new Array();

function createObject() {
	var request_type;
	//var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  request_type=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  request_type=new ActiveXObject("Microsoft.XMLHTTP");
  }
	return request_type;
}
 
var http = createObject();

var operation=new Array (5);
operation[0]="Save";
operation[1]="Update";	
operation[2]="Delete"; 
operation[3]="Approve"; 
operation[4]="Print"; 
 
 var operation_msg=new Array (7);
operation_msg[0]="Data is Saving, Please wait...";
operation_msg[1]="Data is Updating, Please wait...";	
operation_msg[2]="Data is Deleting, Please wait..."; 
operation_msg[3]="Report Generating, Please wait..."; 
operation_msg[4]="List View is Populating, Please wait..."; 
operation_msg[5]="Data is Populating, Please wait..."; 
operation_msg[6]="Mail is Sending, Please wait..."; 
operation_msg[7]="Data Processing, Please wait...";
 
var operation_success_msg=new Array (22);
operation_success_msg[0]="Data is Saved Successfully";
operation_success_msg[1]="Data is Updated Successfully";	
operation_success_msg[2]="Data is Deleted Successfully"; 
operation_success_msg[3]="Report is Generated Successfully"; 
operation_success_msg[4]="List View is Populated Successfully"; 
operation_success_msg[5]="Data is not Saved Successfully";
operation_success_msg[6]="Data is not Updated Successfully";	
operation_success_msg[7]="Data is not Deleted Successfully"; 
operation_success_msg[8]="Report is not Generated Successfully"; 
operation_success_msg[9]="List View is not Populated Successfully"; 
operation_success_msg[10]="Invalid Operation"; 
operation_success_msg[11]="Duplicate Data Found, Please check again."; 
operation_success_msg[12]="Old Password not Matching, Please check again."; 
operation_success_msg[13]="Delete restricted, This Information is used in another Table."; 
operation_success_msg[14]="Update restricted, This Information is used in another Table."; 
operation_success_msg[15]="Database is Busy, Please wait..."; 
operation_success_msg[16]="This Information is already Approved. So You can't change it."; 
operation_success_msg[17]="Issue Qnty Exceeds Stock Qnty."; 
operation_success_msg[18]="Data is Populated Successfully"; 
operation_success_msg[19]="Data is Approved Successfully"; 
operation_success_msg[20]="Data is Un-Approved Successfully"; 
operation_success_msg[21]="Data is not Approved Successfully"; 
operation_success_msg[22]="Data is not Un-Approved Successfully"; 
operation_success_msg[23]="Overlapping Not Allowed, Please Check agian"; 
operation_success_msg[24]="Image Add is Required, Please Save The Image First."; 
operation_success_msg[25]="Total input quantity over the total cut quantity not allowed.";
operation_success_msg[26]="Total output quantity over the total sewing input quantity not allowed."; 
operation_success_msg[27]="Total iron quantity over the total sewing output quantity not allowed."; 
operation_success_msg[28]="Total finishing quantity over the total iron quantity not allowed."; 
operation_success_msg[29]="Total inspection quantity over the total finishing quantity not allowed."; 
operation_success_msg[30]="Total garments quantity over the total inspection quantity not allowed."; 
operation_success_msg[31]="Enrtry quantity can not exceed balance or total quantity."; 
operation_success_msg[32]="Data is  Acknowledged Successfully"; 
operation_success_msg[33]="Data is Un-Acknowledged Successfully"; 
operation_success_msg[34]="Data is Not Acknowledged Successfully"; 
operation_success_msg[35]="Data is Not Un-Acknowledged Successfully"; 

function show_msg( msg )
{
	 
 	 if( !isNumber(msg.replace(/["']/g, "")) ) var msg=10;
	 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
	 {
		$('#messagebox_main', window.parent.document).html(operation_success_msg[msg]).removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
	 });
	 
}

function isNumber (o) {
  return ! isNaN (o-0) && o !== null && o.replace(/^\s\s*/, '') !== "" && o !== false;
}

var time=0;
function freeze_window(msg)
{
	document.getElementById('msg_text').innerHTML=operation_msg[msg];
	
	var id = '#dialog';
	//Get the screen height and width
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();
	//Set height and width to mask to fill up the whole screen
	$('#mask').css({'width':maskWidth,'height':maskHeight});
	//transition effect    
	$('#mask').fadeIn(0);   
	$('#mask').fadeTo("slow",0.8); 
	//Get the window height and width
	var winH = $(window).height();
	var winW = $(window).width();
	//Set the popup window to center
	$(id).css('top',  winH/2-$(id).height()/2);
	$(id).css('left', winW/2-$(id).width()/2);
 	setInterval('count_process_time(document.getElementById("msg").innerHTML)',1000);
	//transition effect
	$(id).fadeIn(0);
	//$(id).fadeOut(9000);
	//setTimeout("$('#mask, .window').hide();",5000);
}

function count_process_time(time)
{
	time=(time*1)+1;
	document.getElementById('msg').innerHTML=time;
}

function release_freezing()
{
	$('#mask, .window').hide();
}

function set_button_status(is_update, permission, submit_func, btn_id, show_print)
{
    if(!show_print) var show_print="";
	permission=permission.split('_');
	
	if (is_update==1)   //Update Mode
	{
		 if (permission[0] == 2 )
		 {
		 	$('#save'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#save'+btn_id).attr('onclick', 'show_no_permission_msg(0)');
		 }
		 else 
		 {
			 $('#save'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#save'+btn_id).attr('onclick', 'show_button_disable_msg(0)');
		 }
		if( permission[1] == 2 )
		{
			 $('#update'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#update'+btn_id).attr('onclick', 'show_no_permission_msg(1)');
		}
		else
		{
			 $('#update'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			 $('#update'+btn_id).attr('onclick', submit_func+'(1)'); 
		}
		if( permission[2] == 2 )
		{
			 $('#Delete'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#Delete'+btn_id).attr('onclick', 'show_no_permission_msg(2)');
		}
		else
		{
			 $('#Delete'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			 $('#Delete'+btn_id).attr('onclick', submit_func+'(2)'); 
		}
		if(permission[3] == 2)
		 {
			  $('#approve'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			  $('#approve'+btn_id).attr('onclick', 'show_no_permission_msg(3)');
		 }
		 else
		 {
			  $('#approve'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			  $('#approve'+btn_id).attr('onclick', submit_func+'(3)');
		 }
		 
		if( permission[4] == 2 )
		{
			 $('#Print'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#Print').attr('onclick', 'show_no_permission_msg(4)');
		}
		else
		{
			 $('#Print'+btn_id).removeClass('formbutton_disabled').addClass('formbutton'); 
			 $('#Print'+btn_id).attr('onclick', submit_func+'(4)');
		}
	}
	else   //New Insert Mode
	{
		 if (permission[0] == 2 )
		 {
		 	$('#save'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#save'+btn_id).attr('onclick', 'show_no_permission_msg(0)');
		 }
		 else 
		 {
			 $('#save'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			 $('#save'+btn_id).attr('onclick', submit_func+'(0)');
		 }
		 if (permission[1] == 2 )
		 {
		 	$('#update'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#update'+btn_id).attr('onclick', 'show_no_permission_msg(1)');
		 }
		 else 
		 {
			 $('#update'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#update'+btn_id).attr('onclick', 'show_button_disable_msg(1)');
		 }
		 if (permission[2] == 2 )
		 {
		 	$('#Delete'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#Delete'+btn_id).attr('onclick', 'show_no_permission_msg(2)');
		 }
		 else 
		 {
			 $('#Delete'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#Delete'+btn_id).attr('onclick', 'show_button_disable_msg(2)');
		 }
		 if (permission[3] == 2 )
		 {
		 	$('#approve'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#approve'+btn_id).attr('onclick', 'show_no_permission_msg(3)');
		 }
		 else 
		 {
			 $('#approve'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#approve'+btn_id).attr('onclick', 'show_button_disable_msg(3)');
		 }
		 
		 if(show_print==1)
		 {
			if( permission[4] == 2 )
			{
				 $('#Print'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
				 $('#Print').attr('onclick', 'show_no_permission_msg(4)');
			}
			else
			{
				 $('#Print'+btn_id).removeClass('formbutton_disabled').addClass('formbutton'); 
				 $('#Print'+btn_id).attr('onclick', submit_func+'(4)');
			} 
		 }
		 else
		 {
			 if ( permission[4] == 2 )
			 {
				$('#Print'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
				$('#Print'+btn_id).attr('onclick', 'show_no_permission_msg(4)');
			 }
			 else 
			 {
				 $('#Print'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
				 $('#Print'+btn_id).attr('onclick', 'show_button_disable_msg(4)');
			 }
		 }
	}  
	return;
}
 
 function get_submitted_variables( flds )
 {
	  var fld_data='';
	 flds=flds.split('*');
	 for (var i=0; i< flds.length; i++)
	 {
		 fld_data=fld_data+'var '+flds[i]+'=  escape(document.getElementById("'+flds[i]+'").value);\n';
	 }
	 return fld_data;
 }
 
 function get_submitted_data_string( flds, path, session )
 {
	  
	 //var p_name = new RegExp("([^a-zA-Z0-9])");  http://blog.ftwr.co.uk/archives/2011/03/18/understanding-complex-regex/
	if(!session) var session=1;
	if(flds=="")
	 {
		 var action="create_menu_session";
	 	 var d= $.ajax({
		  url: path+"includes/common_functions_for_js.php?data="+document.getElementById('active_menu_id').value+"_"+document.getElementById('active_module_id').value+"&action="+action,
		  async: false
		}).responseText
		return;
 	}
	else
	{
		 if (!path) var path="";
		 var fld_data='';
		 flds=flds.split('*');
		 for (var i=0; i< flds.length; i++)
		 {
			 /* if (! document.getElementById(flds[i]).value.search(p_name)) // new RegExp('*!@')).test(document.getElementById(flds[i]).value)))
			  {
				  alert('some field ');
				  return;
			  }*/
			 if(document.getElementById(flds[i]).className=="datepicker hasDatepicker")
			 {
				fld_data=fld_data+'&'+flds[i]+"='"+  change_date_format(trim(document.getElementById(flds[i]).value),path)+"'";
			 }
			 else fld_data=fld_data+'&'+flds[i]+"='"+  encodeURIComponent(trim((document.getElementById(flds[i]).value)))+"'";
		 }
		 if(session==1)
		 {
			 var action="create_menu_session";
			 var d= $.ajax({
				  url: path+"includes/common_functions_for_js.php?data="+document.getElementById('active_menu_id').value+"_"+document.getElementById('active_module_id').value+"&action="+action,
				  async: false
				}).responseText
			}
		}
	 	return (fld_data);
 }

function change_date_format(date, path, new_format, new_sep)
{
	//This function will return newly formatted date String
	// uses  --> echo change_date_format($date,"dd-mm-yyyy","/")
	if(date=="") return '';
	if ( !path) var path="";
	if ( !new_format) var new_format="yyyy-mm-dd";
	if ( !new_sep) var new_sep="-";
	var ddd=date.split("-");
	
	 date=ddd[2]+"-"+ddd[1]+"-"+ddd[0]
	 
	// alert(curr_month);
	
	//else if (new_format=="dd-mm-yyyy") d= curr_date + new_sep + curr_month + new_sep +curr_year ;
	return( date);
	/*if (!new_sep) var new_sep="-";
	if (!new_format) var new_format="yyyy-mm-dd"
	
	var action="change_date_format";
	 return $.ajax({
		  url: path+"includes/common_functions_for_js.php?data="+date+"&action="+action+'&new_sep='+new_sep+'&new_format='+new_format,
		  async: false
		}).responseText*/
}

function show_no_permission_msg(str)
{
	alert('Ask Your admin for '+ operation[str]+' Persmission.');
}

function show_button_disable_msg(str)
{
	return false;
}

function set_date_range(mon)
{
	var year = document.getElementById('cbo_year_selection').value;
	
	 $('.month_button_selected').removeClass('month_button_selected').addClass('month_button');
	 if (mon.substr(0,1)=="0") id_id=mon.replace("0",""); else id_id=mon;
	 $('#btn_'+id_id).removeClass('month_button').addClass('month_button_selected');
	 
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	//var year = currentTime.getFullYear();
	
	var start_date="01" + "-" + mon  + "-" + year;
	var to_date=daysInMonth(mon,year) + "-" + mon  + "-" + year;
	
	document.getElementById('txt_date_from').value=start_date;
	document.getElementById('txt_date_to').value=to_date;		
}

function daysInMonth( month, year ) 
{
	return new Date(year, month, 0).getDate();
}

//------------------------------------------------------------------------- Form Serach List View show starts Here 
function show_list_view( data, action, div, path, extra_func, is_append ) 
{
	//alert(data);
	if (!extra_func) var extra_func="";
	if (!data) var data="0";
	if (!is_append) var is_append="";
	//freeze_window(1);
	document.getElementById(div).innerHTML='<span style="font-size:24px; font-weight:bold; color:#FF0000; margin-top:10px">Please wait, Data is Loading...</span>';
	if( trim(data).length == 0 ) {
		document.getElementById(div).innerHTML = "";
		return;
	}
	var http = createObject();
	http.onreadystatechange = function() {
		if( http.readyState == 4 && http.status == 200 ) {
			//alert(div)
			if ( is_append!=1) document.getElementById(div).innerHTML = http.responseText;
			else  document.getElementById(div).innerHTML += http.responseText;
			eval(extra_func);
			set_all_onclick();
			//release_freezing();	
		}
		
	}
	http.open( "GET", path+".php?data=" + trim( data ) + "&action=" + action, false );
	http.send();
}

//------------------------------------------------------------------------- Supporting Form Value Fill Starts Here
 var ajax = new sack();

//------------------------------------------------------------------------- Form Field Fill Module Entry
function get_php_form_data( id, type, path ) {
	ajax.requestFile = path+'.php?data=' + id + '&action=' + type;	// Specifying which file to get
	ajax.onCompletion = eval_result;	// Specify function that will be executed after file has been found
	ajax.runAJAX();	 
}
function eval_result() {
	//alert(ajax.response );
	eval( ajax.response );
	set_all_onclick();
} 
function sack( file ) {
	this.xmlhttp = null;
	
	this.resetData = function() {
		this.method = "POST";
		this.queryStringSeparator = "?";
		this.argumentSeparator = "&";
		this.URLString = "";
		this.encodeURIString = true;
		this.execute = false;
		this.element = null;
		this.elementObj = null;
		this.requestFile = file;
		this.vars = new Object();
		this.responseStatus = new Array(2);
	};
	
	this.resetFunctions = function() {
		this.onLoading = function() {};
		this.onLoaded = function() {};
		this.onInteractive = function() {};
		this.onCompletion = function() {};
		this.onError = function() {};
		this.onFail = function() {};
	};
	
	this.reset = function() {
		this.resetFunctions();
		this.resetData();
	};
	
	this.createAJAX = function() {
		try {
			this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch( e1 ) {
			try {
				this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch( e2 ) {
				this.xmlhttp = null;
			}
		}
		
		if( !this.xmlhttp ) {
			if( typeof XMLHttpRequest != "undefined" ) this.xmlhttp = new XMLHttpRequest();
			else this.failed = true;
		}
	};
	
	this.setVar = function( name, value ) {
		this.vars[name] = Array( value, false );
	};
	
	this.encVar = function( name, value, returnvars ) {
		if (true == returnvars) return Array( encodeURIComponent( name ), encodeURIComponent( value ) );
		else this.vars[encodeURIComponent( name )] = Array( encodeURIComponent( value ), true );
	}
	
	this.processURLString = function( string, encode ) {
		encoded = encodeURIComponent( this.argumentSeparator );
		regexp = new RegExp( this.argumentSeparator + "|" + encoded );
		varArray = string.split( regexp );
		for( i = 0; i < varArray.length; i++ ) {
			urlVars = varArray[i].split("=");
			if( true == encode ) this.encVar( urlVars[0], urlVars[1] );
			else this.setVar( urlVars[0], urlVars[1] );
		}
	}
	
	this.createURLString = function( urlstring ) {
		if( this.encodeURIString && this.URLString.length ) this.processURLString( this.URLString, true );
		if( urlstring ) {
			if( this.URLString.length ) this.URLString += this.argumentSeparator + urlstring;
			else this.URLString = urlstring;
		}
		
		// prevents caching of URLString
		this.setVar( "rndval", new Date().getTime() );
		
		urlstringtemp = new Array();
		for( key in this.vars ) {
			if( false == this.vars[key][1] && true == this.encodeURIString ) {
				encoded = this.encVar( key, this.vars[key][0], true );
				delete this.vars[key];
				this.vars[encoded[0]] = Array( encoded[1], true );
				key = encoded[0];
			}
			urlstringtemp[urlstringtemp.length] = key + "=" + this.vars[key][0];
		}
		if( urlstring ) this.URLString += this.argumentSeparator + urlstringtemp.join( this.argumentSeparator );
		else this.URLString += urlstringtemp.join( this.argumentSeparator );
	}
	
	this.runResponse = function() {
		eval( this.response );
	}
	
	this.runAJAX = function( urlstring ) {
		if( this.failed ) this.onFail();
		else {
			this.createURLString( urlstring );
			if( this.element ) this.elementObj = document.getElementById( this.element );
			if( this.xmlhttp ) {
				var self = this;
				if( this.method == "GET" ) {
					totalurlstring = this.requestFile + this.queryStringSeparator + this.URLString;
					this.xmlhttp.open( this.method, totalurlstring, false );
				} else {
					this.xmlhttp.open( this.method, this.requestFile, false );
					try {
						this.xmlhttp.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
					}
					catch( e ) {}
				}
				
				this.xmlhttp.onreadystatechange = function() {
					switch( self.xmlhttp.readyState ) {
						case 1:
							self.onLoading();
							break;
						case 2:
							self.onLoaded();
							break;
						case 3:
							self.onInteractive();
							break;
						case 4:
							self.response = self.xmlhttp.responseText;
							self.responseXML = self.xmlhttp.responseXML;
							self.responseStatus[0] = self.xmlhttp.status;
							self.responseStatus[1] = self.xmlhttp.statusText;
							
							if( self.execute ) self.runResponse();
							
							if( self.elementObj ) {
								elemNodeName = self.elementObj.nodeName;
								elemNodeName.toLowerCase();
								if( elemNodeName == "input" || elemNodeName == "select" || elemNodeName == "option" || elemNodeName == "textarea") self.elementObj.value = self.response;
								else self.elementObj.innerHTML = self.response;
							}
							if( self.responseStatus[0] == "200" ) self.onCompletion();
							else self.onError();
							
							self.URLString = "";
							break;
					}
				};
				this.xmlhttp.send(this.URLString);
			}
		}
	};
	
	this.reset();
	this.createAJAX();
} 
//------------------------------------------------------------------------- Supporting Form Value Fill Ends Here
//------------------------------------------------------------------------- Check Numeric Value starts
function IsNumeric( strString ) {
	var strValidChars = "0123456789.";
	var strChar;
	var blnResult = true;
	
	if( strString.length == 0 ) return false;
	
	//test strString consists of valid characters listed above
	for( i = 0; i < strString.length && blnResult == true; i++ ) {
		strChar = strString.charAt(i);
		if( strValidChars.indexOf( strChar ) == -1 ) blnResult = false;
	}
	return blnResult;
}
//------------------------------------------------------------------------- Check Numeric Value Ends

function load_drop_down( plink, data, action, container ) {
	var strURL = plink+".php?data=" + data+"&action=" + action;
 	var http = createObject();
	if( http ) {
		http.onreadystatechange = function() {
			if( http.readyState == 4 ) {
				///alert(strURL+"Sumon"+http.status);
				if( http.status == 200 ){ document.getElementById( container ).innerHTML = http.responseText; set_all_onclick(); }
				
				//else alert("There was a problem while using XMLHTTP:\n" + http.statusText);
			}
		}
		http.open( "GET", strURL, false );
		http.send( null );
	}
}
//------------------------------------------------------------------------- load Drop Down List Value ends
function trim( stringToTrim ) {
	return stringToTrim.replace( /^\s+|\s+$/g, "" );
}

function ltrim( stringToTrim ) {
	return stringToTrim.replace( /^\s+/, "" );
}

function rtrim( stringToTrim ) {
	return stringToTrim.replace( /\s+$/, "" );
}

//------------------------------------------------------------------------- Return Next ID
/*function return_next_id( type ) {
	vid1 = document.getElementById('cbo_module_name').value;
	vid2 = document.getElementById('cbo_root_menu').value;
	vid3 = document.getElementById('cbo_root_menu_under').value;
	type = '1';
	
	var strURL = "../ajax_next_id.php?type=" + type + "&vid1=" + vid1 + "&vid2=" + vid2 + "&vid3=" + vid3;
	 var http = createObject();
	if( http ) {
		http.onreadystatechange = function() {
			if( http.readyState == 4 ) {
				if( http.status == 200 ) document.getElementById('menu_seq_menu_create').innerHTML=http.responseText;
				//else alert( "There was a problem while using XMLHTTP:\n" + http.statusText );
			}
		};			
		http.open( "GET", strURL, false );
		http.send( null );
	}
}*/

//Numeric Value allow field script


//function :: add days for adding some days with a specified date
// param   :: from_date, no_of_days
// return  :: adding date

function add_days( dateObj, byMany, n_format, target_field  )//from_date, no_of_days )
{
	if(!target_field) var target_field="";
	var temp_date="";
	var split_date = dateObj.split("-");
	if(split_date[0].length!=4) dateObj = split_date[2]+"-"+split_date[1]+"-"+split_date[0];
	if (!n_format) var n_format=1;
	var timeU="d";
	var millisecond=1;
	var second=millisecond*1000;
	var minute=second*60;
	var hour=minute*60;
	var day=hour*24;
	var year=day*365;
	dateObj=new Date (dateObj);
	var newDate;
	var dVal=dateObj.valueOf();
	switch(timeU) {
		case "ms": newDate=new Date(dVal+millisecond*byMany); break;
		case "s": newDate=new Date(dVal+second*byMany); break;
		case "mi": newDate=new Date(dVal+minute*byMany); break;
		case "h": newDate=new Date(dVal+hour*byMany); break;
		case "d": newDate=new Date(dVal+day*byMany); break;
		case "y": newDate=new Date(dVal+year*byMany); break;
	}
	if (n_format==1)
		temp_date= $.datepicker.formatDate('dd-mm-yy', newDate);
		
	if (n_format==2)
		temp_date= $.datepicker.formatDate('yy-mm-dd', newDate);
		
	if(target_field!="") document.getElementById(target_field).value=temp_date;	
	else return temp_date;
		
	//return dateFormat(newDate,"yyyy/mm/dd");
}

// ----------------------------end ----------------------------------------

//function :: Date Diff 
// param   :: from_date, To Date
// return  :: Days in Diff
//datediff( $interval, $datefrom, $dateto, $using_timestamps = false ) 

function date_diff( interval, date_form, date_to  )//from_date, no_of_days )
{
	var split_date = date_form.split("-");
	if(split_date[0].length!=4) date_form = split_date[2]+"-"+split_date[1]+"-"+split_date[0];
	
	var split_date = date_to.split("-");
	if(split_date[0].length!=4) date_to = split_date[2]+"-"+split_date[1]+"-"+split_date[0];
	
	
	if (!n_format) var n_format=1;
	//var interval="d";
	var millisecond=1;
	var second=millisecond*1000;
	var minute=second*60;
	var hour=minute*60;
	var day=hour*24;
	//var month=day*30;
	var year=day*365;
	date_form=new Date (date_form);
	date_to=new Date (date_to);
	var newDate;
	//var dVal=dateObj.valueOf();
	switch(interval) {
		case "d": newDate=Math.ceil((date_form.getTime()-date_to.getTime())/(day));  break;
		case "y": newDate=Math.ceil((date_form.getTime()-date_to.getTime())/(year));  break;
	}
	 
		if (newDate>0) return newDate; else return ((newDate)*(-1));
	 
}

function date_compare( fdate, tdate)
{
	var fdate=fdate.split('-');
	var new_date_from=fdate[2]+'-'+fdate[1]+'-'+fdate[0];
	
	var tdate=tdate.split('-');
	var new_date_to=tdate[2]+'-'+tdate[1]+'-'+tdate[0];
	
	var fromDate=new Date(new_date_from);
	var toDate=new Date(new_date_to);
	
	if(toDate.getTime() < fromDate.getTime())
	{
		 return false;
	} 
	else
	{
		return true;
	}
	/*var dt1  = parseInt(fdate.substring(0,2),10);
	var mon1 = parseInt(fdate.substring(3,5),10);
	var yr1  = parseInt(fdate.substring(6,10),10);
	var dt2  = parseInt(tdate.substring(0,2),10);
	var mon2 = parseInt(tdate.substring(3,5),10);
	var yr2  = parseInt(tdate.substring(6,10),10);
	var nfdate = new Date(yr1, mon1, dt1);
	var ntdate = new Date(yr2, mon2, dt2);
	
	if(ntdate < nfdate)
	{
		 return false;
	} 
	else
	{
		return true;
	}*/
}

// ----------------------------end ----------------------------------------

		// reset a form
		// written by ::Fuad
function reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids ) 
{
  // iterate over all of the inputs for the form
  // element that was passed in
 
 // alert(document.getElementById('Delete1').getAttribute('onclick'));
 // return;
 //default_val== "id,val*id,val*id,val"
 if (!extra_func) var extra_func="";
 if (!non_refresh_ids) var non_refresh_ids="";
 if (!default_val) var default_val="";
 
  if (forms!="")
  {
	   forms=forms.split('*');
		for (var i=0; i<forms.length; i++)  
		{
			var form_id=forms[i].split("_");
			//alert(form_id)
			var idd=$('#'+forms[i]).find('.formbutton').attr('id');
			//alert(idd);
			
			var fnc=document.getElementById(idd).getAttribute('onclick').split('(');
			set_button_status(0, permission, fnc[0], form_id[1]);
			 
			non_refresh_ids_arr = non_refresh_ids.split('*');
			
			$('#'+forms[i]).find(':input').each(function() 
			{
				if(jQuery.inArray(this.id, non_refresh_ids_arr)== -1)
				{
					var type = this.type;
					var tag = this.tagName.toLowerCase(); // normalize case
					// it's ok to reset the value attr of text inputs,
					// password inputs, and textareas
					if (type == 'text' || type == 'password' || type == 'hidden' || tag == 'textarea')
					  this.value = "";
					// checkboxes and radios need to have their checked state cleared
					// but should *not* have their 'value' changed
					else if (type == 'checkbox' || type == 'radio')
					  this.checked = false;
					// select elements need to have their 'selectedIndex' property set to -1
					// (this works for both single and multiple select elements)
					else if (type == 'select-one')
					  this.selectedIndex = 0;
					else if (type == 'hidden')
					  this.value = "";
				}
			});
		}
    }
	if (divs!="")
  	{
	   divs=divs.split('*');
		for (var i=0; i<divs.length; i++)  
		{
			document.getElementById(divs[i]).innerHTML="";
		}
	}
	if (fields!="")
  	{
		
	   fields=fields.split('*');
		for (var i=0; i<fields.length; i++)  
		{
			
			var type = document.getElementById(fields[i]).type;
			var tag = document.getElementById(fields[i]).tagName.toLowerCase(); // normalize case
			// it's ok to reset the value attr of text inputs,
		 
			if (type == 'text' || type == 'password' || type == 'textarea')
			  document.getElementById(fields[i]).value = "";
			// checkboxes and radios need to have their checked state cleared
			// but should *not* have their 'value' changed
			else if (type == 'checkbox' || type == 'radio')
			  document.getElementById(fields[i]).checked = false;
			// select elements need to have their 'selectedIndex' property set to -1
			// (this works for both single and multiple select elements)
			else if (type == 'select-one')
			  document.getElementById(fields[i]).selectedIndex = 0;
			else if (type == 'hidden')
			  document.getElementById(fields[i]).value = "";
		}
	}
	
	if (default_val!="")
	{
		default_val=default_val.split('*');
		for (var i=0; i<default_val.length; i++)
		{
			def=default_val[i].split(',');
			if (!def[2])
				document.getElementById(def[0]).value = def[1];
			else
			{
				for (var k=1; k<=def[2]; k++)
				{
					document.getElementById(def[0]+k).value = def[1];
				}
			}
				
		}
	}
	eval(extra_func);
	
}

function disable_enable_fields( flds, operation, loop_flds, loop_leng )
{
	if (!loop_flds) var loop_flds="";
	if (!loop_leng) var loop_leng="";
	if (!flds) var flds="";
	if (!operation) var operation="";
	
	flds=flds.split('*');
	if (operation==0)  // Enable
	{
		for (var i=0; i<flds.length; i++)
		{
			$('#'+ flds[i]).removeAttr('disabled'); 
		}
	}
	else if (operation==1)  // Disable
	{
		for (var i=0; i<flds.length; i++)
		{
			$('#'+ flds[i]).attr('disabled',true);
		}
	}
	loop_flds=loop_flds.split('*');
	for (var i=0; i<flds.length; i++)
	{
		if (operation==0)  // Enable
		{
			for (var k=1; k<=loop_leng; k++)
			{
				$('#'+ loop_flds[i]+k).removeAttr('disabled'); 
			}
		}
		else if (operation==1)  // Enable
		{
			for (var k=1; k<=loop_leng; k++)
			{
				$('#'+ loop_flds[i]+k).attr('disabled',true);
			}
		}
	}
}

function form_validation(control,msg_text) 
{
  // iterate over all of the inputs for the form
  // element that was passed in
 // alert(control);
 //alert(parent.document.getElementById('messagebox_main').innerHTML);
 
	//parent.document.getElementById('messagebox_main').innerHTML=;
	//$('#messagebox_main', window.parent.document).html("sumon");
	
  control=control.split("*");
  msg_text=msg_text.split("*");
  var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
  var new_elem="";
  for (var i=0; i<control.length; i++)
  {  
	  	var type = document.getElementById(control[i]).type;
		var tag = document.getElementById(control[i]).tagName;
		document.getElementById(control[i]).style.backgroundImage="";
		
		if (type == 'text' || type == 'password' || type == 'textarea')
		{
			if (trim(document.getElementById(control[i]).value)=="")
			{
		 	 document.getElementById(control[i]).focus();
	  		 document.getElementById(control[i]).style.backgroundImage=bgcolor;
			 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			 { 
				$(this).html('Please Fill up '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			 });
			 return 0;
			}
		}
		else if (type == 'select-one' || type=='select' )
		{
			//alert(control[i]);
			 if ( trim(document.getElementById(control[i]).value)==0)
			 {
				 document.getElementById(control[i]).focus();
				 document.getElementById(control[i]).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 { 
					$(this).html('Please Select  '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

				 });
				 return 0;
			 }
		}
		else if (type == 'checkbox' || type == 'radio')
		{
			 document.getElementById(control[i]).style.backgroundImage=bgcolor;
			 if (new_elem=="") new_elem=control[i]; else new_elem=new_elem+","+control[i];
		}
		else if (type == 'hidden' )
		{
			if(trim(document.getElementById(control[i]).value)=='')
			{
				if(msg_text[i]!='')
				{
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 { 
						$(this).html('Please Fill up or Select '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
		
					 });
					 return 0;
				}
				else
				{
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 { 
						$(this).html('Please fill up master field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
		
					 });
					 return 0;
					
				}
				 
			}
		}
		
  }
  return 1;
   
}

function change_search_event( mst_type, field_type, qry_array, path ) 
{
	var fld = document.getElementById('cbo_search_by');
	var fld_data  =fld.options[fld.selectedIndex].text;		
	var msg_text="";
	field_type=field_type.split('*');
	qry_array=qry_array.split('*');
	var cntrl_type= field_type[mst_type*1-1];//qry_array[mst_type*1-1];
	if (cntrl_type==0)	msg_text="Please Enter "+fld_data; else  msg_text="Select "+fld_data;
	 
	document.getElementById('search_by_td_up').innerHTML=msg_text;
	//alert(cntrl_type);
	if (cntrl_type==0)
		document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes"	id="txt_search_common"/>';
	else if (cntrl_type==1) // Drop Down query
		document.getElementById('search_by_td').innerHTML=return_global_ajax_value(qry_array[mst_type*1-1], "search_by_drop_down",path);
	else if (cntrl_type==2) // Drop Down array
		document.getElementById('search_by_td').innerHTML=return_global_ajax_value(qry_array[mst_type*1-1], "search_by_drop_down_from_array",path);	
	else  
		document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="datepicker" onfocus="datepicker_()"	id="txt_search_common"/>';	
}

function return_global_ajax_value( data, action, path, page_name) {
 
	if (!page_name) var page_name="";
	if (page_name=="")  page_name='includes/common_functions_for_js';
  return $.ajax({
      url: path+page_name+".php?data="+data+"&action="+action,
      async: false
    }).responseText
}

function datepicker_()
{
	 
	$( ".datepicker" ).datepicker({
					dateFormat: 'dd-mm-yy',
					changeMonth: true,
					changeYear: true
				});
			 
}

function print_report( data, action, path ) 
{
	 
	if (!data) var data="0";
	
	if( data == "" ) {
		return;
	}
	var http = createObject();
	http.onreadystatechange = function() {
		if( http.readyState == 4 && http.status == 200 ) {
				var response = http.responseText;
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(response);					
				d.close();
		}
	}
	http.open( "GET", path+".php?data=" +  data  + "&action=" + action, false );
	http.send();
}


 function set_hotkey()
 {

	/*if ($('#index_page', window.parent.document).val()=="" || $('#index_page', window.parent.document).val()=="undefined")
	{
		alert('asd');
		document.body.innerHTML="";
	}*/
	document.onkeydown = function(e) {
            e = e || window.event; // because of Internet Explorer quirks...
            k = e.which || e.charCode || e.keyCode; // because of browser differences...
			 
			 
			// alert($('#'+document.activeElement.id).attr('class'));
				var form_id=$('#'+document.activeElement.id).closest('form').attr('id').split("_");
				
				 //alert(form_id[1]);
				
				if ($('#'+document.activeElement.id).attr('class')!='flt') { if (k==13) return false; }
				
				if (k == 83 && e.ctrlKey )   // save   control + s
				{
					$('#save'+form_id[1]).click();
					return false; 
				}
				else if (k == 85 && e.ctrlKey )  // Update   control + u
				{
					$('#update'+form_id[1]).click();
					return false; 
				}  
				else if (k == 68 && e.ctrlKey )  // Delete   control + d
				{
					$('#Delete'+form_id[1]).click();
					return false; 
				} 
				else if (k == 82 && e.ctrlKey )  // Refresh   control + r
				{
					 
					 $('#Refresh'+form_id[1]).click();
					return false; 
				} 
				/*else if (k == 120 && e.ctrlKey )  // Refresh   control + r
				{
					 
					//return_next_id_module( type );
					return_next_id();
					return false; 
				} */
				else  return true;
			   // we processed the event, stop now.
		 
	}
 }
  
 
 /*
 //------------------------------------------------------------------------- Form Refresh and Back Starts Here 
function checkKeycode( e, type ) {
	var keycode;
	var type = type;
	//alert(e);
	if( window.event ) {
		keycode = window.event.keyCode;
		if( keycode == 116 ) window.event.keyCode = 0;
	}
	else if( e ) keycode = e.which;
	
	if( keycode == 114 ) return false;
	else if( keycode == 116 ) return false;
	else if( keycode == 117 ) {
		window.event.keyCode = 0;
		return false;
	}
	else if( keycode == 8 ) {
		window.event.keyCode = 0;
		return false;
	}
	else if( keycode == 120 ) {
		return_next_id_module( type );
		return_next_id();
	}
}
//------------------------------------------------------------------------- Form Refresh and Back Ends Here 
*/


function file_uploader ( url, mst_id, det_id,  form, file_type, is_multi, show_button )
{
	
	if (file_type=="" || file_type==0 ) file_type=1;
	if (!is_multi) var is_multi=0;
	if (!show_button) var show_button=1;
	
	if (mst_id=="" || mst_id==0 ) 
	{
		 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
		 { 
			$(this).html('Please Select or Save any Information before File Upload.').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

		 });
		return false;
	}
	
	var im_url="";
	if (url.length==3)im_url=""; else if (url.length==6)im_url="../"; else if (url.length==9)im_url="../../"; else if (url.length==12)im_url="../../../"; 
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', url+'includes/common_functions_for_js.php?action=file_uploader&det_id='+det_id+'&form='+form+'&is_multi='+is_multi+'&mst_id='+mst_id+'&file_type='+file_type+'&show_button='+show_button, 'File Uploader', 'width=640px,height=330px,center=1,resize=0,scrolling=0', im_url )
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("txt_selected_id")
		var order_no=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
		if (title=="Company Selection")
		{
			document.getElementById('cbo_unit_name_show').value=order_no.value;	
			document.getElementById('cbo_unit_name').value=order_id.value;	
		}
		else
		{
			document.getElementById('cbo_user_buyer_show').value=order_no.value;	
			document.getElementById('cbo_user_buyer').value=order_id.value;	
		}
		 
	}
}

function confirm_msg_box( msg, btn_type, url )
{
	var im_url="";
	if (url.length==3)im_url=""; else if (url.length==6)im_url="../"; else if (url.length==9)im_url="../../"; else if (url.length==12)im_url="../../../"; 
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', url+'includes/common_functions_for_js.php?action=confirm_msg_box&msg='+msg+'&btn_type='+btn_type, 'Confirm Box', 'width=240px,height=130px,center=1,resize=0,scrolling=0', im_url )
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var action=this.contentDoc.getElementById("txt_action")
		return (action.value);
	}
}

function return_ajax_request_value(data, action, path) {
  return $.ajax({
      url: path+".php?data="+data+"&action="+action,
      async: false
    }).responseText
}

function set_conversion_rate( cid, cdate, path, dest_id) {
	if(!path) var path='';
	if(!dest_id || dest_id==undefined) var dest_id='';
	var dd= $.ajax({
      url: path+"includes/common_functions_for_js.php?cid="+cid+'&cdate='+cdate+"&action=return_conversion_date",
      async: false
    }).responseText;
//	alert(dd)
	if(dest_id!='')
			$('#'+dest_id).val(trim(dd));
		else
			return dd;
}

function math_operation( target_fld, value_fld, operator, fld_range, dec_point)
{
	//number_format_common( number, dec_type, comma, path, currency )
	//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
	//	math_operation( des_fil_id, field_id, '+', rowCount,ddd);
		
	if (!dec_point) var dec_point=0;
	//alert('eeeeee');
	//alert(dec_point.currency);
	
	if(!fld_range) var fld_range="";
	if (fld_range=="")
	{
		value_fld=value_fld.split('*');
		var tot="";
		if (operator=="+")
		{
			
			for (var i=0;i<value_fld.length; i++)
			{
				tot=(tot*1)+(document.getElementById(value_fld[i]).value*1)
			}
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency) ; //(document.getElementById(value_fld[0]).value*1)+(document.getElementById(value_fld[1]).value*1)
		}
		
		else if (operator=="-")
		{
			var tot =(document.getElementById(value_fld[0]).value*1)-(document.getElementById(value_fld[1]).value*1);
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency);
		}
		else if (operator=="*")
		{
			var tot=1;
			for (var i=0;i<value_fld.length; i++)
			{
				tot=(tot*1)*(document.getElementById(value_fld[i]).value*1)
			}
			//document.getElementById(target_fld).value=tot.toFixed(4); //(document.getElementById(value_fld[0]).value*1)*(document.getElementById(value_fld[1]).value*1)
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency); //(document.getElementById(value_fld[0]).value*1)*(document.getElementById(value_fld[1]).value*1)
		}
		else if (operator=="/")
		{
			//document.getElementById(target_fld).value=((document.getElementById(value_fld[0]).value*1)/(document.getElementById(value_fld[1]).value*1)).toFixed(4)
			var tot=((document.getElementById(value_fld[0]).value*1)/(document.getElementById(value_fld[1]).value*1))
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency) ;
		}
	}
	else
	{
		//alert(dec_point);
		//alert(value_fld);
		//alert(target_fld);
		var tot=0;
		for (var i=1; i<=fld_range; i++)
		{
			
			tot=(tot*1) + (document.getElementById(value_fld+i).value*1);
			
		}
		//document.getElementById(target_fld).value=tot.toFixed(2);
		document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma,dec_point.currency);

	}
}


//    All ACtivities of **********************************************************************************************FUNCTIONS_BOTTOM.js


function set_all_onclick()
{
		
 																	// To Change Background Color of Validated Field\
 
jQuery(".text_boxes").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});

 jQuery(".text_area").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
  jQuery(".combo_boxes").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
  jQuery(".text_boxes_numeric").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
jQuery(".datepicker").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
 // To Change Background Color of Validated Field ends
 
 
jQuery(".text_boxes_numeric").keypress(function(e) {
	
	var c = String.fromCharCode(e.which);
 	var evt = (e) ? e : window.event;
    var key = (evt.keyCode) ? evt.keyCode : evt.which;
	if(key != null) key = parseInt(key, 10);
	var allowed = '1234567890.'; // ~ replace of Hash(#)
		if (isUserFriendlyChar(key)) return true
		else if (key != 8 && key !=0 && allowed.indexOf(c) < 0) 
			return false;
		else if (!numeric_valid( $(this).attr('id'), 0))
			return false;
	  
   
});

jQuery(".text_boxes_numeric").blur(function(e) {
	numeric_valid( $(this).attr('id'), 1)
});

function numeric_valid( id, from)
{
	var txt=$('#'+id).val();//.split('.');
	var dotposl=txt.lastIndexOf(".");
	var dotposf=txt.indexOf(".");
	if (dotposl!=dotposf)
	{
		var txt_d=$('#'+id).val().substr(0,dotposl);
		$('#'+id).val(txt_d);//alert(txt_d);
		numeric_valid( id, from)
	}
	else return true; 
}

function isUserFriendlyChar(val) {
      // Backspace, Tab, Enter, Insert, and Delete
      if(val == 8 || val == 9 || val == 13 || val == 46 )//  || val == 45 Insert
        return true;
	// Ctrl, Alt, CapsLock, Home, End, and Arrows
      if((val > 16 && val < 21) || (val > 34 && val < 41))
        return true;
	// The rest
      return false;
}
 //Numeric Text Box Validation Ends
 
																		// Special Character Validation
jQuery(".text_boxes").keypress(function(e) {
     var c = String.fromCharCode(e.which);
	 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+()[]{};:# '; // ~ replace of Hash(#)
	 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
	  	return false; 
	
});

 
  	$('.text_boxes').blur(function(e) {
	    var target = e.target || e.srcElement;
   		document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
	   });
  
  																		 
jQuery(".text_area").keypress(function(e) {
     var c = String.fromCharCode(e.which);
	 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+()[]{};:# '; // ~ replace of Hash(#)
	 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
	  	return false; 
	
});

  	$('.text_area').blur(function(e) {
	    var target = e.target || e.srcElement;
   		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
	   });
  
 
 // Special Character Validation Ends
 
	 															 // Global Date Picker Initialisaton
		  $( ".datepicker" ).datepicker({
					dateFormat: 'dd-mm-yy',
					changeMonth: true,
					changeYear: true
				});
		
	 // Datapickker ENds
 
 				function set_bangla()
				{
					$(".bangla").bnKb({
						'switchkey': {"webkit":"k","mozilla":"y","safari":"k","chrome":"k","msie":"y"},
						'driver': phonetic
					});
				}
				
		if(isMobile.any()) {  // 09-03-2015
	   //alert("This is a Mobile Device");
		   $(".text_boxes").each(function( index ) {
				 var ttl=$(this).attr('onDblClick');
				 if(!ttl) var ttl=""; else ttl=ttl+";"
				$(this).attr('onClick',ttl);
			});
			
			$(".text_boxes_numeric").each(function( index ) {
				 var ttl=$(this).attr('onDblClick');
				 if(!ttl) var ttl=""; else ttl=ttl+";"
				$(this).attr('onClick',ttl);
			});
		}		
          
	}
	


function number_format_common( number, dec_type, comma, currency )
{
	if (currency==undefined) var currency="";
	if (currency!="")
	{
		if (currency==1) dec_type=4; else dec_type=5;
	}
	var dec_place= new Array;
	dec_place[1]=2;
	dec_place[2]=2;
	dec_place[3]=8;
	dec_place[4]=2;
	dec_place[5]=4;
	dec_place[6]=0;
	dec_place[7]=2;
	return number_format (number, dec_place[dec_type],'.' , "") ;
}

function number_format (number, decimals, dec_point, thousands_sep) 
{
   
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');    }
    return s.join(dec);
}
//========================Defined by Monzu==============================================
function array_key_exists (key, search) 
{
  if (!search || (search.constructor !== Array && search.constructor !== Object)) {
    return false;
  }
  return key in search;
}

function array_sum (array) 
{
  var key, sum = 0;
  if (array && typeof array === 'object' && array.change_key_case) { 
    return array.sum.apply(array, Array.prototype.slice.call(arguments, 0));
  }
  if (typeof array !== 'object') {
    return null;
  }

  for (key in array) {
    if (!isNaN(parseFloat(array[key]))) {
      sum += parseFloat(array[key]);
    }
  }
  return sum;
}
 
 
 
function show_graph( settings_file, data_file, chart_type, div, caption, rel_path, is_link, gheight, gwidth )
{
	//document.getElementById('stack_company').style.visibility="hidden";
	//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '', 500,900 );
	//alert(data_file);
	var dd=data_file.split("\n");
	 
	var chart_settings=new Array();
	var chart_flash=new Array();
	
	chart_settings['pie']="<settings><background><file></file></background><data_type>csv</data_type><legend><enabled>0</enabled></legend><pie><inner_radius>25</inner_radius><height>10</height><angle>10</angle><gradient></gradient></pie><animation><start_time>1</start_time><pull_out_time>1</pull_out_time></animation><data_labels><show>{title}</show><max_width>300</max_width><min_width>600</min_width></data_labels></settings>";
	
	chart_settings['column']="<settings><background><alpha>2000</alpha><border_alpha>20</border_alpha><color>FAFAFA</color></background><grid><category><dashed>1</dashed></category><value><dashed>1</dashed></value></grid><axes><category><width>1</width><color>000000</color></category><value><width>1</width><color>000000</color></value></axes><values><value><min>0</min></value></values><depth>15</depth><column><width>85</width><balloon_text>{title}: {value} Person</balloon_text><grow_time>3</grow_time></column><graphs><graph gid='1'><title>Present</title><color>CFAFDD</color> </graph><graph gid='2'><title>Absent</title><color>CFAA55</color> </graph></settings>";
	
	 
	//alert(chart_settings[chart_type]);
	
	
	chart_flash['pie']="ampie";
	chart_flash['column']="amcolumn";
	//document.getElementById('caption_text').innerHTML="Monthly Order Status (Quantity Wise) for Year: ";
	//alert(is_link);
	//data_file=escape(data_file);
	//alert(data_file);
	var params = 
	{
		bgcolor:"#CCCCCC"
	};
	if (is_link!="")
	{
		var flashVars = 
		{
			path: rel_path+"ext_resource/amcharts/flash/", 
			settings_file: settings_file+".php",
			data_file: data_file+".php"
		}; 
	}
	else
	{
		var flashVars = 
		{
			path: rel_path+"ext_resource/amcharts/flash/", 
			chart_data: data_file,
        	chart_settings: chart_settings[chart_type]
		};		
	}
	
	// change 8 to 80 to test javascript version            
	if (swfobject.hasFlashPlayerVersion("8"))
	{
		//alert(rel_path+"---"+chart_flash[chart_type]);
		swfobject.embedSWF( rel_path+"ext_resource/amcharts/flash/"+chart_flash[chart_type]+".swf", div, gwidth, gheight, "8.0.0", "../../../amcharts/flash/expressInstall.swf", flashVars, params );
		
	}
	else
	{
		//alert(chart_flash[chart_type]);
		// Note, as this example loads external data, JavaScript version might only work on server
		var amFallback = new AmCharts.AmFallback();
		amFallback.pathToImages = "../../../amcharts/javascript/images/";
		amFallback.settingsFile = flashVars.settings_file;
		amFallback.dataFile = flashVars.data_file;				
		amFallback.type = chart_type; //"column";
		amFallback.write(div);
	}
}

function accordion_menu( hid, div_id, onclick_fnc, is_collapse) 
{
	if (!is_collapse) var is_collapse=""; 
	if (is_collapse==1)
	{
		$(".accord_close").each(function() {
			if ($(this).attr('id')!=div_id) $(this).hide();
		});
	}
	$(".accordion_h").each(function() {
		
		 var tid=$(this).attr('id'); 
		 tid=tid+"span";
 
		 if (!$('#'+tid).length)
			 var dd=$(this).html();
		 else
		  	var dd=$('#'+tid).html();
		 
		if ($(this).attr('id')!=hid)
		{
			dd=dd.replace("-","+");
			 if (!$('#'+tid).length)
				 $(this).html(dd);
			 else
				$('#'+tid).html(dd);
	 
		}
	});
	
	if (!onclick_fnc) var onclick_fnc="";
	 
	 tid=hid+"span";
	 
	 if (!$('#'+tid).length)
		 var dd=$('#'+hid).html();
	 else
		var dd=$('#'+tid).html();
 
	if (!$('#'+tid).length)
	{
		if (dd.indexOf("+")>0)  dd=dd.replace("+","-"); else dd=dd.replace("-","+");
		$('#'+hid).html(dd);
	}
	 else
	 {
		if (dd=="+") $('#'+tid).html("-"); else  $('#'+tid).html("+");
	 }
		
	$('#'+div_id).toggle('slow', function() { 
		eval (onclick_fnc);
	});
}

function append_report_checkbox(tid,is_resize)
{
	var i=0;
	$("#"+tid+" thead th").each(function() {
		$(this).addClass('res'+i)
		$(this).prepend('<input type="checkbox" id="'+tid+"_"+i+'" name="rept_check_box[]" class="rpt_check" onclick="report_check_box_click(this.id)" value="1" checked="checked" />');
		i++
    });
	
	$("#scroll_body table tr").each(function() {
		j=0;
		$(this).find("td").each(function(){
			$(this).addClass('res'+j)
			j++;	
		});
    });
	$("#report_table_footer tr").each(function() {
		j=0;
		$(this).find("th").each(function(){
			$(this).addClass('res'+j)
			j++;	
		});
    });
	 if (is_resize==1)
	 {
		 table_column_resize(tid);
	 }
}

function report_check_box_click(id)
{
	if ($('#'+id).is(":checked")==false)
	{
		$('#'+id).removeAttr('checked');
		$('#'+id).removeClass("rpt_check");
	}
	else
	{
		$('#'+id).attr('checked','checked');
		$('#'+id).addClass('rpt_check');
	}
}

function print_priview_html( report_div, scroll_div, header_table, footer_table, report_type, link_pos, rel_path,extra_func )  //type: 1=xls, 2=pdf, 3=html
{
	var filter=0;
	 
	if (report_type==3)
	{
		var mxheght= document.getElementById(scroll_div).style.maxHeight;
		var total_width=0;
		var top_wd=$("#"+header_table).width();
		var botom_wd=$("#"+scroll_div+ " table").width();
		var top_wd_new=0;
		var botom_wd_new=0;
			var i=0;
			$("#"+header_table+" thead th").each(function() {
				var wd=($(this).width());
				if (!$('#'+header_table+"_"+i).hasClass('rpt_check'))
				{
				  total_width=(total_width*1)+(wd*1);
					$("#"+header_table).find("tr").each(function(){
						$(this).find("th:eq("+i+")").hide();
					   
					});
					$("#"+scroll_div+" table").find("tr").each(function(){
						$(this).find("td:eq("+i+")").hide();
					});
					$("#"+footer_table).find("tfoot tr").each(function(){
						$(this).find("th:eq("+i+")").hide();
					   
					});
					
				}
				i++;
			});
			top_wd_new=top_wd-total_width;
			botom_wd_new=botom_wd-total_width;
			
			 if ($("#"+scroll_div +" table tr:first").attr('class')=='fltrow') 
			 {
				filter=1;
				$("#"+scroll_div +" table tr:first").hide();	 
			 }
			 $("#"+header_table).width(top_wd_new);
			 $("#"+scroll_div+ " table").width(botom_wd_new);
			  
			 document.getElementById(scroll_div).style.overflow="auto";
			 document.getElementById(scroll_div).style.maxHeight="none";
			  
			var html = document.getElementById(report_div).innerHTML;
			
			 /*var tto1=($(html).find('a').replaceWith(function() {
				return this.innerHTML;
			}).end().html());
			alert(tto1);*/
			 var tto=($(html).find('input').replaceWith(function() {
				return this.innerHTML;
			}).end().html());  			
			 // alert(tto);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			var btn='';//<input type="button" onclick="javascript:window.print()" value="  Print  " name="Print" class="formbutton" style="width:100px"/><br><br>
			 
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><script src="'+rel_path+'includes/functions.js"></script><script src="'+rel_path+'js/jquery.js"></script><link rel="stylesheet" href="'+rel_path+'css/style_common.css" type="text/css" media="print" /><style type="text/css">p{word-break:break-all;word-wrap: break-word;width:100%;}</style><title></title></head><body><div id="report_on_popup">'+btn+tto+'</div></body</html>');
			d.close();
			 
			document.getElementById(scroll_div).style.overflowY="scroll";
	 		document.getElementById(scroll_div).style.maxHeight=mxheght;
			if ( filter==1)
			{
				$("#"+scroll_div +" table tr:first").show();
			}
			i=0;
			$("#"+header_table+" thead th").each(function() {
				if (!$('#th_'+i).hasClass('check'))
				{
				  	$("#"+header_table).find("tr").each(function(){
						$(this).find("th:eq("+i+")").show(); 
					});
					$("#"+scroll_div+" table").find("tr").each(function(){
						$(this).find("td:eq("+i+")").show();
					});
					
					$("#"+footer_table).find("tfoot tr").each(function(){
						$(this).find("th:eq("+i+")").show();
					   
					});
				}
				i++;
			});
			 $("#"+header_table).width(top_wd);
			 $("#"+scroll_div+ " table").width(botom_wd);
		 
	}
	else if ( report_type==1  || report_type==2)
	{
		var original_html = document.getElementById(report_div).innerHTML;
		var mxheght= document.getElementById(scroll_div).style.maxHeight;
		var total_width=0;
		var top_wd=$("#"+header_table).width();
		var botom_wd=$("#"+scroll_div+ " table").width();
		var top_wd_new=0;
		var botom_wd_new=0;
			var k=0;
			var idd;
			$("#"+header_table+" thead th").each(function() {
				var wd=($(this).width());
				
				if (!$('#'+header_table+"_"+k).hasClass('rpt_check'))
				{
					 //alert(k+"-"+$('#'+header_table+"_"+k).hasClass('rpt_check'))
				  	total_width=(total_width*1)+(wd*1);
					$("#"+header_table).find("tr").each(function(){
						$(this).find("th:eq("+k+")").addClass('out_of_report') ;
					});
					$("#"+scroll_div+" table").find("tr").each(function(){
						$(this).find("td:eq("+k+")").addClass('out_of_report') ;
					});
					$("#"+footer_table).find("tfoot tr").each(function(){
						$(this).find("th:eq("+k+")").addClass('out_of_report');
					});
				}
				k++;
			});
			$(".out_of_report").remove();
			$(".rpt_check").remove();
			  
			top_wd_new=top_wd-total_width;
			botom_wd_new=botom_wd-total_width;
			
			 if ($("#"+scroll_div +" table tr:first").attr('class')=='fltrow') 
			 {
				filter=1;
				$("#"+scroll_div +" table tr:first").remove();	 
			 }
			 
			 $("#"+header_table).width(top_wd_new);
			 $("#"+scroll_div+ " table").width(botom_wd_new);
			  
			 document.getElementById(scroll_div).style.overflow="auto";
			 document.getElementById(scroll_div).style.maxHeight="none";
			  
			var html = document.getElementById(report_div).innerHTML;
			 var tto=($(html).find('a').replaceWith(function() {
				return this.innerHTML;
			}).end().html());
			 
			// alert(tto);
			 $.post(rel_path+"includes/common_functions_for_js.php",
			  { path: rel_path, action: "generate_report_file", htm_doc: tto },
			  function(data){
				window.open(rel_path+data, "#");
			  }
			);
			 document.getElementById(report_div).innerHTML="" ;
			 document.getElementById(report_div).innerHTML=original_html ;
			  if ($("#"+scroll_div +" table tr:first").attr('class')=='fltrow') 
			 {
				$("#"+scroll_div +" table tr:first").remove();	 
			 }
			 if (!tableFilters) var tableFilters="";
			 setFilterGrid('table_body',-1,tableFilters);
			   
	}
	 
}

function print_priview_html2( report_div, scroll_div, header_table, footer_table, report_type, link_pos, rel_path, extra_func, top_table )  //type: 1=xls, 2=pdf, 3=html
{ 
		var filter=0;
		var header_arr=header_table.split(",");
		var footer_arr=footer_table.split(","); 
		var scroll_arr=scroll_div.split(",");
		var top_wd_arr=[];
		var bottom_wd_arr=[];
		var p="";	
		if (report_type==3)
		{
			for(p=0;p<header_arr.length;p++)
			{ 

				var mxheght= document.getElementById(scroll_arr[p]).style.maxHeight;
				var total_width=0;
				var top_wd=$("#"+header_arr[p]).width();				
				var botom_wd=$("#"+scroll_arr[p]+ " table").width();
				top_wd_arr[header_arr[p]]=top_wd;
				bottom_wd_arr[header_arr[p]]=botom_wd;
				var top_wd_new=0;
				var botom_wd_new=0;
				var i=0;
				$("#"+header_arr[p]+" thead th").each(function() {
					var wd=($(this).width());
					if (!$('#'+header_arr[p]+"_"+i).hasClass('rpt_check'))
					{
					  total_width=(total_width*1)+(wd*1);
					 // alert (wd);
					 
						$("#"+header_arr[p]).find("tr").each(function(){
							$(this).find("th:eq("+i+")").hide();
						   
						});
						$("#"+scroll_arr[p]+" table").find("tr").each(function(){
							$(this).find("td:eq("+i+")").hide();
						});
						$("#"+footer_arr[p]).find("tfoot tr").each(function(){
							$(this).find("th:eq("+i+")").hide();
						   
						});
						
					}
					i++;
				});
 				top_wd_new=top_wd-total_width;
				botom_wd_new=botom_wd-total_width;
				
				 if ($("#"+scroll_arr[p] +" table tr:first").attr('class')=='fltrow') 
				 {
					filter=1;
					$("#"+scroll_arr[p] +" table tr:first").hide();	 
				 }
				 if(top_table) $("#"+top_table).width(top_wd_new);
				 $("#"+header_arr[p]).width(top_wd_new);
				 $("#"+scroll_arr[p]+ " table").width(botom_wd_new);
				  
				 document.getElementById(scroll_arr[p]).style.overflow="auto";
				 document.getElementById(scroll_arr[p]).style.maxHeight="none";
			}
				var html = document.getElementById(report_div).innerHTML;
				//alert(html);
				//alert(html);
				 /*var tto1=($(html).find('a').replaceWith(function() {
					return this.innerHTML;
				}).end().html());
				alert(tto1);*/
				 var tto=($(html).find('input').replaceWith(function() {
					return this.innerHTML;
				}).end().html());  			
				 //alert(tto);
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				var btn='';//<input type="button" onclick="javascript:window.print()" value="  Print  " name="Print" class="formbutton" style="width:100px"/><br><br>
				 
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><script src="'+rel_path+'includes/functions.js"></script><script src="'+rel_path+'js/jquery.js"></script><link rel="stylesheet" href="'+rel_path+'css/style_common.css" type="text/css" media="print" /><style type="text/css">p{word-break:break-all;word-wrap: break-word;width:100%;}th,td{font-size:13px;}</style><title></title></head><body><div id="report_on_popup">'+btn+tto+'</div></body</html>');
				d.close();

		    for(p=0;p<header_arr.length;p++)
			{
				 
				document.getElementById(scroll_arr[p]).style.overflowY="scroll";
		 		document.getElementById(scroll_arr[p]).style.maxHeight=mxheght;
				if ( filter==1)
				{
					$("#"+scroll_arr[p] +" table tr:first").show();
				}
				i=0;
				$("#"+header_arr[p]+" thead th").each(function() {
					if (!$('#th_'+i).hasClass('check'))
					{
					  	$("#"+header_arr[p]).find("tr").each(function(){
							$(this).find("th:eq("+i+")").show(); 
						});
						$("#"+scroll_arr[p]+" table").find("tr").each(function(){
							$(this).find("td:eq("+i+")").show();
						});
						
						$("#"+footer_arr[p]).find("tfoot tr").each(function(){
							$(this).find("th:eq("+i+")").show();
						   
						});
					}
					i++;
				});  
				 $("#"+header_arr[p]).width(top_wd_arr[header_arr[p]]);
				 $("#"+scroll_arr[p]+ " table").width(bottom_wd_arr[header_arr[p]]);
			}
			 
		}
		else if ( report_type==1  || report_type==2)
		{
			//alert (report_type);
			var original_html = document.getElementById(trim(report_div)).innerHTML;

 			for(p=0;p<header_arr.length;p++)
			{	
				var k=0;				
				var mxheght= document.getElementById(scroll_arr[p]).style.maxHeight;
				var total_width=0;
				var top_wd=$("#"+header_arr[p]).width();
				var botom_wd=$("#"+scroll_arr[p]+ " table").width();
				top_wd_arr[header_arr[p]]=top_wd;
				bottom_wd_arr[header_arr[p]]=botom_wd;
				var top_wd_new=0;
				var botom_wd_new=0;
				
				var idd;
				$("#"+header_arr[p]+" thead th").each(function() {

					var wd=($(this).width());
					
					if (!$('#'+header_arr[p]+"_"+k).hasClass('rpt_check'))
					{
						 //alert(k+"-"+$('#'+header_table+"_"+k).hasClass('rpt_check'))
					  	total_width=(total_width*1)+(wd*1);
						//alert (total_width);
						$("#"+header_arr[p]).find("tr").each(function(){
							$(this).find("th:eq("+k+")").addClass('out_of_report') ;
						});
						$("#"+scroll_arr[p]+" table").find("tr").each(function(){
							$(this).find("td:eq("+k+")").addClass('out_of_report') ;
						});
						$("#"+footer_arr[p]).find("tfoot tr").each(function(){
							$(this).find("th:eq("+k+")").addClass('out_of_report');
						});
					}
					k++;
				});
				
				  
				top_wd_new=top_wd-total_width;
				botom_wd_new=botom_wd-total_width;
				
				 if ($("#"+scroll_arr[p] +" table tr:first").attr('class')=='fltrow') 
				 {
					filter=1;
					$("#"+scroll_arr[p] +" table tr:first").remove();	 
				 }
				 
				 $("#"+header_arr[p]).width(top_wd_new);
				 $("#"+scroll_arr[p]+ " table").width(botom_wd_new);
				  
				 document.getElementById(scroll_arr[p]).style.overflow="auto";
				 document.getElementById(scroll_arr[p]).style.maxHeight="none";
			}
			    $(".out_of_report").remove();
				$(".rpt_check").remove();
				  
				var html = document.getElementById(report_div).innerHTML;
  				var tto=($(html).find('a').replaceWith(function() {
					return this.innerHTML;
				}).end().html());

				 $.post(rel_path+"includes/common_functions_for_js.php",
				  { path: rel_path, action: "generate_report_file", htm_doc: tto },
				  function(data){
					window.open(rel_path+trim(data), "#");
				  }
				);
				 document.getElementById(report_div).innerHTML="" ;
				 document.getElementById(report_div).innerHTML=original_html ;
				 for(p=0;p<header_arr.length;p++)
				 { 
					 if ($("#"+scroll_arr[p] +" table tr:first").attr('class')=='fltrow') 
					 {
						$("#"+scroll_arr[p] +" table tr:first").remove();	 
					 }
				 }
				 if (!tableFilters) var tableFilters="";
				 setFilterGrid('table_body',-1,tableFilters);
				   
		}	 	
	 
}		

function set_session_large_post(data, rel_path, action )
{
	if (!action) var action="save_post_session";
		$.post(rel_path+"includes/common_functions_for_js.php",
			  { path: rel_path, action: action, data: data },
			  function(data){
				//alert(data);
			  }
			);
}

function table_column_resize(tble_id)
{
	  
	 $(function(){
        var pressed = false;
        var start = undefined;
        var startX, startWidth;
		var classid;
        $("#"+tble_id+" th").mousedown(function(e) {

            start = $(this);
            classid=$(start).attr('class');
            pressed = true;
            startX = e.pageX;
            startWidth = $(this).width();
            startX1 = e.pageX;
        });

        $(document).mousemove(function(e) {
            if(pressed) {
                $('.'+classid).width(startWidth+(e.pageX-startX));
            }
        });

        $(document).mouseup(function() {
            if(pressed) {
                pressed = false;
            }
        });
    });
}

function report_convert_button(url)
{
	return '<input onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 1, \'0\',\''+url+'\' )" type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="button" onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 3, \'0\',\''+url+'\' )" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
}

function change_color(v_id,e_color)
{
	var clss;
	$('td').click(function() {
		var myCol = $(this).index();
		clss='res'+myCol;
	
	});
	
	if (document.getElementById(v_id).bgColor=="#33CC00")
	{
		document.getElementById(v_id).bgColor=e_color;
		$('.'+clss).removeAttr('bgColor');
	}
	else
	{
		document.getElementById(v_id).bgColor="#33CC00";
		$('.'+clss).attr('bgColor','#33CC00');
	}
}


function get_dropdown_text( fid )
{
	//uses: var data=get_dropdown_text( 'fld_id' );
	//alert( document.getElementById(fid).type);
	if( document.getElementById(fid).value!='' )
	{
		var sel = document.getElementById(fid);		
		 return sel.options[sel.selectedIndex].text;	
	}
}


var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

function fnc_generate_Barcode( valuess,img_id )
{
	var value = valuess;//$("#barcodeValue").val();
	var btype = 'code39';//$("input[name=btype]:checked").val();
	var renderer ='bmp';// $("input[name=renderer]:checked").val();
	var settings = {
	  output:renderer,
	  bgColor: '#FFFFFF',
	  color: '#000000',
	  barWidth: 1,
	  barHeight: 60,
	  moduleSize:5,
	  posX: 10,
	  posY: 20,
	  addQuietZone: 1
	};
	$("#"+img_id).html('11');
	 value = {code:value, rect: false};
	$("#"+img_id).show().barcode(value, btype, settings);
} 