
// cookie.js와 jeditable 포함!

// 구글 moior.com analytics 추적소스
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43199106-1', 'moior.com');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

/*// 구글 analytics 이벤트 트래킹- 과거 소스 !!! va r _gaq = _gaq || [];  _g aq.push(['_setAc count', 'UA-431990 06-1']);  _g aq.push(['_trackPa geview']); */

// php의 숫자형식함수
function number_format(num){
	num = num.toString();
 	num = num.replace(/,/gi, "");
    var num_str = num.toString();
	var result = "";
 
	for(var i=0; i<num_str.length; i++){
        var tmp = num_str.length - (i+1);
 
        if(((i%3)==0) && (i!=0))    result = ',' + result;
        result = num_str.charAt(tmp) + result;
    } 
    return result;
	/*var reg = /(^[+-]?\d+)(\d{3})/;
	str += '';
	while (reg.test(str)) str = str.replace(reg, '$1' + ',' + '$2');
	return str;   간단버전 */
}
// 숫자만리턴
function onlyNumber(str) {
	return String(str).replace(/[^0-9]/g,'');
}
/* 8시면 08 */
function zerofill(num, zero) 
{ 
    if(!zero) zero = 2; 
    var z = '', i; 
    for(i = 0; i < zero; i ++) z += '0'; 
    z += num;
    return z.substring(z.length - zero); 
} 
/* 숫자를 한글돈으로 */
function moneyString(iAmount){
	iAmount = onlyNumber(iAmount);
	unit1 = new Array("","일","이","삼","사","오","육","칠","팔","구");
	unit2 = new Array("십","백","천");
	unit3 = new Array("만","억","조");
	var obj = iAmount;
	var chk_unit3 = false;

	total=obj.length;   //<!-- 입력한 숫자의 길이를 변수에 담습니다. -->
	rs="";      //<!-- 숫자를 한글로 변환한 내용을 담을 변수를 생성합니다. -->
	for(i=0; i<total ;i++) {
		num=obj.substring(i,i+1);
		temp1=total-i;
		for(j=0; j<unit1.length; j++){  //<!-- 숫자를 한글로 변환합니다. -->
			if(num == j)
			rs+=unit1[j];
		}
		if(num != 0 && (total-1) != i){  //<!-- 숫자가 0이면 한글로 변환하지않습니다. 그리고 마지막 숫자에는 단위를  붙이지 않습니다. -->
			//<!-- 십,백,천 단위를 붙입니다. -->
			if(obj.substring(0,(total-i)).length%4 == 0)
				rs+=unit2[2];
			if(obj.substring(0,(total-i)).length%4 == 3)
				rs+=unit2[1];
			if(obj.substring(0,(total-i)).length%4 == 2)
				rs+=unit2[0];
		}
		//<!-- 만 ,억,조 단위를 붙입니다. -->
		if((num != 0 && (total-1) != i) || chk_unit3 == false){ // SK
			if(temp1 == 5){		rs+=unit3[0]; chk_unit3 = true;}
			else if(temp1 ==9){	rs+=unit3[1]; chk_unit3 = true;}
			else if(temp1 == 13){rs+=unit3[2]; chk_unit3 = true;}
		}
	}
	//result.style.visibility="visible";  <!-- 숨겨진 result를 보이게 합니다. -->
	//result.innerText = rs;   <!-- result에 지정한 텍스트를 입력합니다. -->
	return (rs);
}

/* 디버깅함수 */
var adiv = document.createElement('div');
adiv.id = "debug_adiv"; adiv.style.position = "absolute"; adiv.style.top = "100px";
adiv.style.background = "yellow"; 
function a(a) {

	document.body.appendChild(adiv);
	document.getElementById("debug_adiv").innerHTML += "<div style='border-bottom:2px solid #0000FF;padding:10px'>"+a + "</div>";

}
function aa(a) {alert(a);}
function p(arr){
 	var str = "";
	if(typeof(arr)=='object'){
		for(var i in arr){
			if(typeof(arr[i])=='object'){
				str += "◆ "+i + "<br> \n";
				for(var j in arr[i])
					str += "\t&nbsp; &nbsp; " + j + " = " + arr[i][j] + " \n"; 
			}else
				str += "* "+i + " = " + arr[i] + " \n"; 
		}
	}else{ str = arr+'';}

	document.body.appendChild(adiv);
	document.getElementById("debug_adiv").innerHTML += "<div style='border-bottom:2px solid #0000FF;padding:10px'>["+ typeof(arr)+' 형태]<br /> '+ nl2br(str)+'' + "</div>";
}
function echo(str){ document.write(str); }
function e(str){ Echo(str); }

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

// 한글. 문자열의 길이를 구하는 함수 
function strlen_ko(str) { 
  var len = 0;
  for(var i=0;i<str.length;i++) {
    len += (str.charCodeAt(i) > 128) ? 2 : 1;
  }
  return len;
}
// 한글. 문자열에서 시작위치와 길이를 주면 substring 하여 리턴한다. 
function substring_ko(str,start,size) {
	var lim = 0;
	var pos = 0;
	var beg = 0;
	var minus = 0;
	var len = strlen_ko(str);
  
	// 시작위치까지 skip하는 로직 
	for(var i=0;pos<start;i++) {
		pos += (str.charCodeAt(i) > 128) ? 2 : 1;
	}
	beg = i;
	  
	// 시작위치에서 길이만큼 잘라내는 로직
	for (var i=beg; i<len; i++) { 
		lim += (str.charCodeAt(i) > 128) ? 2 : 1;
		if (lim > size || i == len-beg-1 ) { 
			return str.substring(beg,i);  
		}
	}
}
/*초성만 남기기*/
function cho_hangul(str) {
	var cho = ["ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ"];
	result = "";
	for(i=0;i<str.length;i++) {
		code = str.charCodeAt(i)-44032;
		if(code>-1 && code<11172) result += cho[Math.floor(code/588)];
	}
	return result;
}


// pType - 'D':일수, 'M':개월수 // 오류있음. 연도 바뀌면 12아니라 11
function date_diff(pStartDate, pEndDate, pType) {  
	/*pStartDate = pStartDate.replace("-","");
    pEndDate   = pStartDate.replace("-","");*/
    var strSDT = new Date(pStartDate);  
    var strEDT = new Date(pEndDate); 
    var strTermCnt = 0;  

    if(pType == 'D') {  //일수 차이  
        strTermCnt = (strEDT.getTime()-strSDT.getTime())/(1000*60*60*24);
    } else {            //개월수 차이  
        //년도가 같으면 단순히 월을 마이너스 한다.  
        // => 20090301-20090201 의 경우(윤달이 있는 경우) 아래 else의 로직으로는 정상적인 1이 리턴되지 않는다.  
        if(pEndDate.substring(0,4) == pStartDate.substring(0,4)) {  
            strTermCnt = strEDT.getMonth() * 1 - strSDT.getMonth() * 1 //3개월x일 차이;
            if(strSDT.getDay() < strEDT.getDay() ){ // 6월2일 vs 9월 3일
            	strTermCnt = strTermCnt; //3개월 & n일 차이;
            }else strTermCnt = strTermCnt -1 ; // 6월2일 vs 9월 1일 > 2개월 29일 차이
        } else {  
            //strTermCnt = Math.floor((strEDT.getTime()-strSDT.getTime())/(1000*60*60*24*365.25/12));  
            strTermCnt = Math.round((strEDT.getTime()-strSDT.getTime())/(1000*60*60*24*365/12));  
        }  
    }  
    return strTermCnt;  
}
/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.

 today = new Date();
 var dateString = today.format("dd-m-yy");
 alert(dateString);
 */

var dateFormat = function () {
    var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
        timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
        timezoneClip = /[^-+\dA-Z]/g,
        pad = function (val, len) {
            val = String(val);
            len = len || 2;
            while (val.length < len) val = "0" + val;
            return val;
        };

    // Regexes and supporting functions are cached through closure
    return function (date, mask, utc) {
        var dF = dateFormat;

        // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
        if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
            mask = date;
            date = undefined;
        }

        // Passing date through Date applies Date.parse, if necessary
        date = date ? new Date(date) : new Date;
        if (isNaN(date)) throw SyntaxError("invalid date");

        mask = String(dF.masks[mask] || mask || dF.masks["default"]);

        // Allow setting the utc argument via the mask
        if (mask.slice(0, 4) == "UTC:") {
            mask = mask.slice(4);
            utc = true;
        }

        var _ = utc ? "getUTC" : "get",
            d = date[_ + "Date"](),
            D = date[_ + "Day"](),
            m = date[_ + "Month"](),
            y = date[_ + "FullYear"](),
            H = date[_ + "Hours"](),
            M = date[_ + "Minutes"](),
            s = date[_ + "Seconds"](),
            L = date[_ + "Milliseconds"](),
            o = utc ? 0 : date.getTimezoneOffset(),
            flags = {
                d:    d,
                dd:   pad(d),
                ddd:  dF.i18n.dayNames[D],
                dddd: dF.i18n.dayNames[D + 7],
                m:    m + 1,
                mm:   pad(m + 1),
                mmm:  dF.i18n.monthNames[m],
                mmmm: dF.i18n.monthNames[m + 12],
                yy:   String(y).slice(2),
                yyyy: y,
                h:    H % 12 || 12,
                hh:   pad(H % 12 || 12),
                H:    H,
                HH:   pad(H),
                M:    M,
                MM:   pad(M),
                s:    s,
                ss:   pad(s),
                l:    pad(L, 3),
                L:    pad(L > 99 ? Math.round(L / 10) : L),
                t:    H < 12 ? "a"  : "p",
                tt:   H < 12 ? "am" : "pm",
                T:    H < 12 ? "A"  : "P",
                TT:   H < 12 ? "AM" : "PM",
                Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
            };

        return mask.replace(token, function ($0) {
            return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
        });
    };
}();


