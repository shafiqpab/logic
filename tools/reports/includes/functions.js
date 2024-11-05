function show_div( div ) {
	document.getElementById(div).style.visibility = 'visible';
}

function hide_div( div ) {
	document.getElementById(div).style.visibility = 'hidden';
}
//------------------------------------------------------------------------- Form Serach List View show starts Here 
function showResult( str, type, div ) {
	if( str.length == 0 ) {
		document.getElementById(div).innerHTML = "";
		return;
	}
	if( window.XMLHttpRequest ) xmlhttp = new XMLHttpRequest();	// code for IE7+, Firefox, Chrome, Opera, Safari
	else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");		// code for IE6, IE5
	
	xmlhttp.onreadystatechange = function() {
		if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
			document.getElementById(div).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open( "GET", "../list_view.php?q=" + trim( str ) + "&type=" + type, true );
	xmlhttp.send();
}
//------------------------------------------------------------------------- User Privi List view start
function showResult_userpriv( str, str2, type, div ) {
	if( str.length == 0 ) {
		document.getElementById(div).innerHTML = "";
		return;
	}
	if( str2.length == 0 ) {
		document.getElementById(div).innerHTML = "";
		return;
	}
	
	if( window.XMLHttpRequest ) xmlhttp = new XMLHttpRequest();	// code for IE7+, Firefox, Chrome, Opera, Safari
	else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");		// code for IE6, IE5
	
	xmlhttp.onreadystatechange = function() {
		if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
			document.getElementById(div).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open( "GET", "../list_view.php?q=" + trim( str ) + "&qq=" + trim( str2 ) + "&type=" + type, true );
	xmlhttp.send();
}
//------------------------------------------------------------------------- Form search List View  and show Ends Here 
//------------------------------------------------------------------------- Load page in a div from drop down search
function loadpage_data( user_id, module_id ) {
	if( user_id == 0 || module_id == 0 ) {
		document.getElementById("user_prev").innerHTML = "";
	}
	else {
		if( window.XMLHttpRequest ) xmlhttp = new XMLHttpRequest();	// code for IE7+, Firefox, Chrome, Opera, Safari
		else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");		// code for IE6, IE5
		
		xmlhttp.onreadystatechange = function() {
			if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
				document.getElementById("user_prev").innerHTML = xmlhttp.responseText;
			}
		}
		xmlhttp.open( "GET", "../show_previledge.php?user_id=" + user_id + "&module_id=" + module_id, true );
		xmlhttp.send();
	}
}
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

//------------------------------------------------------------------------- Supporting Form Value Fill Starts Here 
var ajax = new sack();
var currentClientID = false;
//------------------------------------------------------------------------- Form Field Fill Module Entry
function getClientData( id, type ) {
	ajax.requestFile = '../get_data_update.php?getClientId=' + id + '&type=' + type;	// Specifying which file to get
	ajax.onCompletion = showClientData;	// Specify function that will be executed after file has been found
	ajax.runAJAX();	
}

function showClientData() {
	var formObj = document.forms['asd'];
	eval( ajax.response );
}
//------------------------------------------------------------------------- Form Field User Id Update
function user_id_update( id, type ) {
	//alert(id+type);return;
	ajax.requestFile = '../get_data_update.php?getClientId=' + id + '&type=' + type;	// Specifying which file to get
	ajax.onCompletion = showClientData_user_id_update;	// Specify function that will be executed after file has been found
	ajax.runAJAX();	
}

function showClientData_user_id_update() {
	var formObj = document.forms['user_creation_form'];
	eval( ajax.response );
}
//------------------------------------------------------------------------- Form Field Fill Menu Entry

//------------------------------------------------------------------------- Form Field Fill Menu Entry
function getMenuData( user_id, id, getClientId, type, form_id ) {
	ajax.requestFile = '../get_data_update.php?user_id=' + user_id + '&id=' + id + '&getClientId=' + getClientId + '&type=' + type + '&form_id=' + form_id;
	ajax.onCompletion = showmenuData;
	ajax.runAJAX();
}

function showmenuData() {
	eval( ajax.response );
}
//------------------------------------------------------------------------- Form Field Fill User Entry Employee
function get_emp_user_Data( id, type ) {
	var clientId = id;
	ajax.requestFile = 'get_data_update.php?getClientId=' + clientId + '&type=' + type;
	ajax.onCompletion = showuser_emp;
	ajax.runAJAX();
}

function showuser_emp() {
	var formObj = document.forms['user_create'];
	eval( ajax.response );
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
					this.xmlhttp.open( this.method, totalurlstring, true );
				} else {
					this.xmlhttp.open( this.method, this.requestFile, true );
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
//------------------------------------------------------------------------- load Drop Down List Value Starts
function getXMLHTTP() { //fuction to return the xml http object
	var xmlhttp = false;	
	try {
		xmlhttp = new XMLHttpRequest();
	}
	catch( e ) {		
		try {
			xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch( e ) {
			try {
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch( e1 ) {
				xmlhttp = false;
			}
		}
	}
	return xmlhttp;
}

function load_drop_down( distId, type, div ) {
	var strURL = "ajax_dropdown_loader.php?type=" + type + "&distId=" + distId;
	var req = getXMLHTTP();
	if( req ) {
		req.onreadystatechange = function() {
			if( req.readyState == 4 ) {
				if( req.status == 200 ) document.getElementById( div ).innerHTML = req.responseText;
				else alert("There was a problem while using XMLHTTP:\n" + req.statusText);
			}
		}
		req.open( "GET", strURL, true );
		req.send( null );
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
function return_next_id( type ) {
	vid1 = document.getElementById('cbo_module_name').value;
	vid2 = document.getElementById('cbo_root_menu').value;
	vid3 = document.getElementById('cbo_root_menu_under').value;
	type = '1';
	
	var strURL = "../ajax_next_id.php?type=" + type + "&vid1=" + vid1 + "&vid2=" + vid2 + "&vid3=" + vid3;
	var req = getXMLHTTP();
	
	if( req ) {
		req.onreadystatechange = function() {
			if( req.readyState == 4 ) {
				if( req.status == 200 ) document.getElementById('menu_seq_menu_create').innerHTML=req.responseText;
				else alert( "There was a problem while using XMLHTTP:\n" + req.statusText );
			}
		};			
		req.open( "GET", strURL, true );
		req.send( null );
	}
}

function return_next_id_module() {
	type = '2';
	var strURL = "../ajax_next_id.php?type=" + type;
	var req = getXMLHTTP();
	
	if( req ) {
		req.onreadystatechange = function() {
			if( req.readyState == 4 ) {
				if( req.status == 200 ) document.getElementById('mod_seq_mod_create').innerHTML = req.responseText;
				else alert( "There was a problem while using XMLHTTP:\n" + req.statusText );
			}
		}
		req.open( "GET", strURL, true );
		req.send( null );
	}
}


//Numeric Value allow field script
function numbersonly(myfield, e, dec)
{
	
	var key;
	var keychar;

	if (window.event)
   		key = window.event.keyCode;
	else if (e)
    	key = e.which;
	else
   		return true;
	keychar = String.fromCharCode(key);

	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
    return true;
	
	// numbers
	else if ((("0123456789.").indexOf(keychar) > -1))
   		return true;
	else
    	return false;
}

//function :: add days for adding some days with a specified date
// param   :: from_date, no_of_days
// return  :: adding date

function add_days(from_date, no_of_days)
{
	
    from_date = from_date.split(/\D+/);
    from_date = new Date(from_date[0],from_date[1]-1,(parseInt(from_date[2])+(no_of_days*1)));
    var ndateArr = from_date.toString().split(' ');
    var Months = 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec';
    var adding_date = ndateArr[3]+'-'+(parseInt(Months.indexOf(ndateArr[1])/4)+1)+'-'+ndateArr[2];
    return (adding_date);
}

// ----------------------------end ----------------------------------------

		// reset a form
		// written by :: m@mit
       function reset_form(form) 
       { 
          // iterate over all of the inputs for the form
          // element that was passed in
         $('#'+form).find(':input').each(function() {
            var type = this.type;
            var tag = this.tagName.toLowerCase(); // normalize case
            // it's ok to reset the value attr of text inputs,
            // password inputs, and textareas
            if (type == 'text' || type == 'password' || tag == 'textarea')
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
          });
       }

	function fn_get_data_selected_emp(id,type)
	{
		ajax.requestFile = '../get_data_update.php?getClientId=' + id + '&type=' + type;	// Specifying which file to get
		ajax.onCompletion = showfn_get_data_selected_emp;	// Specify function that will be executed after file has been found
		ajax.runAJAX();	
	}
	function showfn_get_data_selected_emp() {
		//var formObj = document.forms['asd'];
		eval( ajax.response );
	}