
$(document).ready(function () {	
	$('.number_only').bind('paste input', allowNumbersOnly);
	$('.number_only_dot').bind('paste input', allowNumbersOnlywithDot);
	$('.number_only_comma').bind('paste input', allowNumbersOnlywithComma);
	$('.email_only').bind('paste input', allowEmailOnly);
	$('.phone_only').bind('paste input', allowPhoneOnly);
	$('.alpha_numeric').bind('paste input', allowAlphaNumeric);
	$('.alpha_only').bind('paste input', allowAlphaOnly);
	$('.name_only').bind('paste input', allowNameOnly);
	$('.splname_only').bind('paste input', allowCompName);
	$('.address_only').bind('paste input', allowAddress);			$.fn.setCursorPosition = function(pos) {	  this.each(function(index, elem) {		if (elem.setSelectionRange) {		  elem.setSelectionRange(pos, pos);		} else if (elem.createTextRange) {		  var range = elem.createTextRange();		  range.collapse(true);		  range.moveEnd('character', pos);		  range.moveStart('character', pos);		  range.select();		}	  });	  return this;	};		$.fn.selectRange = function(start, end) 	{		return this.each(function() 		{			if (this.setSelectionRange) {				this.focus();				this.setSelectionRange(start, end);			} else if (this.createTextRange) {				var range = this.createTextRange();				range.collapse(true);				range.moveEnd('character', end);				range.moveStart('character', start);				range.select();			}		});	};
});

function allowNumbersOnly(e) {
    var self = $(this);
    setTimeout(function () {
        var initVal = self.val(),
            outputVal = initVal.replace(/[^0-9]/g, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowNumbersOnlywithDot(e) {
	var self = $(this);
	var regex = /[^0-9\.]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowNumbersOnlywithComma(e) {
	var self = $(this);
	var regex = /[^0-9\,]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowEmailOnly(e) {
	var self = $(this);
	var regex = /[^@a-zA-Z0-9._-]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowPhoneOnly(e) {
	var self = $(this);
	var regex = /[^ 0-9-+]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowAlphaNumeric(e) {
	var self = $(this);
	var regex = /[^ a-zA-Z0-9]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowAlphaOnly(e) {
	var self = $(this);
	var regex = /[^ a-zA-Z]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowNameOnly(e) {
	var self = $(this);
	var regex = /[^ a-zA-Z.]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowCompName(e) {
	var self = $(this);
	var regex = /[^ a-zA-Z0-9.,&_-]/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}
function allowAddress(e) {
	var self = $(this);
	var regex = /[^ a-zA-Z0-9.,&_+-/()]\n/g;
	setTimeout(function () {
        var initVal = self.val(),
        outputVal = initVal.replace(regex, "");
        if (initVal != outputVal) self.val(outputVal);
    });
}

function getNum(val) {
   var val = parseFloat(val);
   if (isNaN(val)) {
     return 0;
   }
   return val;
}
function zeroPad(num, places) {
  var zero = places - num.toString().length + 1;
  return Array(+(zero > 0 && zero)).join("0") + num;
}


