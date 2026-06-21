// Xin Calendar 2.15 (In-Page Core)
// Copyright 2004  Xin Yang    All Rights Reserved.

// Last Modified: 11-Dec-2006
// Web Site: yxScripts.com
// Email: m_yangxin@hotmail.com

// the following copyright display settings should remain intact all the time if you are not a licensed user
// otherwise the use of Xin Calendar 2 is illegal
// --------------------------------------
var xcCalCopyright="Copyright 2004 Xin Yang";
var xcCalHome="http://www.yxScripts.com";
var xcCopyrightDisplay="&#169;";
var xcShowCopyright=1;
// --------------------------------------

var xcCore=1;
var xcCalPool=new Array(), xcSpecialCalls=new Array(), xcRefPool={};
var xcDateFormatRE=xcMonthList=xcDayList=null;
var xcOtherResize=null;
var xcMouseX=xcMouseY=xcOnResizeChecked=0;
var xcUseIFRAME=-1;
var xcOldMouseMove=null;

var xcFootTagWidth=["", "100%", "50%", "33%", "25%", "20%"];
var xcTableAttr0=" CELLPADDING='0' CELLSPACING='0' BORDER='0'>";
var xcTableAttr1=" CELLPADDING='0' CELLSPACING='0' BORDER='0' ALIGN='CENTER'>";
var xcTableAttr2=" CELLPADDING='0' CELLSPACING='"+xcGridWidth+"' BORDER='0' ALIGN='CENTER'>";
var xcTableOpen0="<TABLE"+xcTableAttr0;
var xcTableOpen1="<TABLE"+xcTableAttr1;
var xcTableOpen2="<TABLE WIDTH='100%'"+xcTableAttr1;
var xcTableOpen3="<TABLE WIDTH='100%'"+xcTableAttr2;
var xcTableClose="</TABLE>";
var xcTROpen="<TR VALIGN='TOP'>";
var xcTROpen2="<TR ALIGN='CENTER' VALIGN='MIDDLE'>";
var xcTRClose="</TR>";
var xcCSSOpen='this.className="';
var xcCSSClose='"';

var xcTableRE=/TR|TBODY|THEAD|TFOOT|TABLE/i;

var xcInternalDateFormat="yyyymmdd";
var xcECell="E", xcRCell="R", xcCCell="1", xcOCell="0";

var xcNav=navigator.userAgent.toLowerCase();
var xcVer=parseInt(navigator.appVersion);

var xcIsMac=(xcNav.indexOf("mac")!=-1);
var xcIsOpera=(xcNav.indexOf("opera")!=-1);
var xcIsSafari=(xcNav.indexOf("safari")!=-1);
var xcIsKon=(xcNav.indexOf("konqueror")!=-1);

var xcIsIE=(!xcIsOpera && !xcIsKon && xcNav.indexOf("msie")!=-1);
var xcIsIE4=(xcIsIE && xcNav.indexOf("msie 4")!=-1);
var xcIsIE5=(xcIsIE && !xcIsIE4);
var xcIsIE55=(xcIsIE && xcNav.indexOf("msie 5.5")!=-1);
var xcIsIE6=(xcIsIE && xcNav.indexOf("msie 6")!=-1);
var xcIsIE55up=(xcIsIE55 || xcIsIE6);
var xcIsIEMac=xcIsIE && xcIsMac;

var xcIsN4=(xcNav.indexOf('mozilla')!=-1 && xcNav.search(/msie|gecko|opera|spoofer|compatible|webtv|hotjava/)==-1);
var xcIsN6=(xcNav.indexOf("gecko")!=-1 && !xcIsSafari);
var xcIsO7=(xcIsOpera && xcNav.search(/opera[\s\/]+(\d+)/)!=-1?parseFloat(RegExp.$1)>=7:false);
var xcIsK3=(xcIsKon && xcVer>=3);

var xcCalSafe=(xcIsN6 || xcIsIE5 || xcIsK3 || xcIsO7 || xcIsSafari) && !xcIsN4 && !xcIsIE4;

var _dayNormal=new Array(), _dayNormalCurrent=new Array(), _dayNormalSpecial=new Array();
var _dayOther=new Array(), _dayOtherCurrent=new Array(), _dayOtherSpecial=new Array();

// browser functions
function xcVoid() { return true; }
function xcResizeCheck() { setTimeout("xcMoveCalendars()",100); }
function xcMoveCalendars() {
  for (var i=0; i<xcCalPool.length; i++) {
    var curCal=xcCalPool[i];
    if (curCal.holder.style.visibility=="visible" && curCal.mode!=2) {
      var l=xcGetHolder(curCal.holderId);
      if (l!=null) {
        xcMoveLayerTo(curCal.holder,curCal.dx+xcGetLayerX(l),curCal.dy+xcGetLayerY(l));
      }
      else {
        xcHideCal(i);
      }
    }
  }

  xcOtherResize();
}
function xcTrackMouseMove(e) {
  if (xcIsIE || xcIsK3 || xcIsOpera) {
    xcMouseX=event.clientX+(xcIsK3?0:(document.documentElement?document.documentElement.scrollLeft:document.body.scrollLeft));
    xcMouseY=event.clientY+(xcIsK3?0:(document.documentElement?document.documentElement.scrollTop:document.body.scrollTop));
  }
  else {
    xcMouseX=e.pageX; xcMouseY=e.pageY;
  }

  if (xcOldMouseMove) { xcOldMouseMove(e); }
}

function xcUseIFrame() {
  if (xcUseIFRAME<0) { xcUseIFRAME=document.getElementsByTagName("SELECT").length+document.getElementsByTagName("OBJECT").length+document.getElementsByTagName("APPLET").length+document.getElementsByTagName("EMBED").length; }
  return (xcUseIFRAME>0);
}

// layer functions
function xcGetHolder(id) { return id==""?null:document.getElementById(id); }
function xcGetLayerX(l,hasTD) {
  if (xcIsIEMac) {
    if (xcTableRE.test(l.tagName)) {
      hasTD=1;
    }

    var x=l.offsetLeft;
    if (l.tagName=="TD" && typeof(hasTD)=="undefined") {
      x+=xcGetLayerX(l.parentElement,1);
    }
    else if (l.offsetParent) {
      x+=xcGetLayerX(l.offsetParent,hasTD);
    }
    else {
      x+=isNaN(parseInt(document.body.style.marginLeft))?parseInt(document.body.leftMargin):parseInt(document.body.style.marginLeft);
    }
    return x;
  }
  else {
    return l.offsetLeft+(l.offsetParent?xcGetLayerX(l.offsetParent):0);
  }
}
function xcGetLayerY(l,hasTD) {
  if (xcIsIEMac) {
    if (xcTableRE.test(l.tagName)) {
      hasTD=1;
    }

    var x=l.offsetTop;
    if (l.tagName=="TD" && typeof(hasTD)=="undefined") {
      x+=xcGetLayerY(l.parentElement,1);
    }
    else if (l.offsetParent) {
      x+=xcGetLayerY(l.offsetParent,hasTD);
    }
    else {
      x+=isNaN(parseInt(document.body.style.marginTop))?parseInt(document.body.topMargin):parseInt(document.body.style.marginTop);
    }
    return x;
  }
  else {
    return l.offsetTop+(l.offsetParent?xcGetLayerY(l.offsetParent):0);
  }
}

function xcWriteLayer(l,content) { l.innerHTML=content; }
function xcMoveLayerTo(l,x,y) {
  l.style.top=y+"px";
  l.style.left=x+"px";
}
function xcMoveLayerBy(l,x,y) {
  l.style.top=(parseInt(l.style.top)+y)+"px";
  l.style.left=(parseInt(l.style.left)+x)+"px";
}
function xcShowLayer(l) { l.style.visibility="visible"; }
function xcHideLayer(l) { l.style.visibility="hidden"; }

function xcMakeLayer() {
  var l=document.createElement("DIV");

  with (l.style) {
    position="absolute";
    visibility="hidden";
    left="-1000px"; top="-1000px";
    zIndex=++xcBaseZIndex;
  }

  if (xcIsIE && !xcIsMac) {
    document.body.insertBefore(l,document.body.firstChild);
  }
  else {
    document.body.appendChild(l);
  }

  l.iframe=null;
  if (xcIsIE55up && xcUseIFrame() && !xcIsMac) {
    l.iframe=document.createElement("IFRAME"); l.iframe.src="javascript:false";
    with (l.iframe.style) {
      position="absolute";
      visibility="hidden";
      left="-1000px"; top="-1000px";
      width="20px"; height="20px";
      zIndex=l.style.zIndex-1;
      filter="progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)";
    }

    document.body.insertBefore(l.iframe,l);
    l.iframe.sized=false;
  }

  return l;
}

// helper functions
function xcGetDD(n) { return ((n<10)?"0":"")+n; }
function xcDayShort(y,m,d) { return xcWeekdayShortNames[(new Date(y,m,d)).getDay()]; }
function xcGetMonths() {
  var monthList={}
  for (var i=0; i<xcMonthShortNames.length; i++) {
    monthList[xcMonthShortNames[i].toLowerCase()]=i;
  }
  return monthList;
}
function xcMonthFromName(mon) {
  if (xcMonthList==null) {
    xcMonthList=xcGetMonths();
  }
  return xcMonthList[mon.toLowerCase()]||0;
}
function xcGetDays() {
  var dayList={}
  for (var i=0; i<xcWeekdayShortNames.length-1; i++) {
    dayList[xcWeekdayShortNames[i].toLowerCase()]=i;
  }
  return dayList;
}
function xcDayFromName(day) {
  if (xcDayList==null) {
    xcDayList=xcGetDays();
  }
  return xcDayList[day.toLowerCase()]||0;
}

function xcTagOpen(tag,style,events,title) { return "<"+tag+" "+events+" "+(title?"TITLE='"+title.replace(/'/g,'&#39;')+"' ":"")+(style?"CLASS='"+style+"'":"")+">"; };
function xcDIVOpen(style,events,title) { return xcTagOpen("DIV",style,events||"",title||""); }; var xcDIVClose="</DIV>";
function xcDIV(style,content,events,title) { return xcDIVOpen(style,events||"",title||"")+content+xcDIVClose; }
function xcTDDIV(style,content,width,events,title) { return "<TD"+(width!=""?" WIDTH='"+width+"'":"")+">"+xcDIV(style,content,events||"",title||"")+"</TD>"; }

function xcSort(a,b) { return a.order-b.order; }

function xcGetInternalDate(y,m,d) {
  return y+xcGetDD(m+1)+xcGetDD(d);
}

// calendar functions
function xcFindCal(idx) { return xcCalPool[idx]; }

function xcGetCal(refKey,targetField,refField,defaultDate,holderId,dx,dy,mode,name) {
  var curCal=null;
  for (var i=0; i<xcCalPool.length; i++) {
    if (xcCalPool[i].vacant || xcCalPool[i].targetField==targetField && targetField!=null || xcMultiCalendar==0 && xcCalPool[i].mode==1) {
      curCal=xcCalPool[i];
      curCal.vacant=false;
      break;
    }
  }

  if (curCal==null) {
    curCal=new xcCalOBJ(xcCalPool.length);
    xcCalPool[xcCalPool.length]=curCal;
  }
  else {
    curCal.reset();
  }

  curCal.conf=xcRefPool[refKey]||{};
  curCal.targetField=targetField||null; curCal.refField=refField||targetField;
  curCal.defaultDate=defaultDate||"";
  curCal.holderId=holderId||"";
  curCal.dx=dx||0; curCal.dy=dy||0;
  curCal.mode=mode;
  if (name) {
    curCal.name=name;
  }
  else {
    curCal.name="xc"+(new Date()).getTime();
  }

  return curCal;
}

function xcRegisterConf(refKey,modKey,attrKey,value,mode) {
  if (typeof(xcRefPool[refKey])=="undefined") {
    xcRefPool[refKey]={}
  }
  if (typeof(xcRefPool[refKey][modKey])=="undefined") {
    xcRefPool[refKey][modKey]={};
  }
  if (mode==0) {
    xcRefPool[refKey][modKey][attrKey]=value;
  }
  else if (mode==1) {
    if (typeof(xcRefPool[refKey][modKey][attrKey])=="undefined") {
      xcRefPool[refKey][modKey][attrKey]=new Array();
    }
    xcRefPool[refKey][modKey][attrKey][xcRefPool[refKey][modKey][attrKey].length]=value;
  }
  else if (mode==2) {
    if (typeof(xcRefPool[refKey][modKey][attrKey])=="undefined") {
      xcRefPool[refKey][modKey][attrKey]="";
    }
    xcRefPool[refKey][modKey][attrKey]+=value;
  }
}

function _xcGetDateFormatRE(f) {
  var dateFormat=f||xcDateFormat;

  dateFormat=dateFormat.replace(/\\/g, '\\\\');
  dateFormat=dateFormat.replace(/\//g, '\\\/');
  dateFormat=dateFormat.replace(/\[/g, '\\\[');
  dateFormat=dateFormat.replace(/\]/g, '\\\]');
  dateFormat=dateFormat.replace(/\(/g, '\\\(');
  dateFormat=dateFormat.replace(/\)/g, '\\\)');
  dateFormat=dateFormat.replace(/\{/g, '\\\{');
  dateFormat=dateFormat.replace(/\}/g, '\\\}');
  dateFormat=dateFormat.replace(/\</g, '\\\<');
  dateFormat=dateFormat.replace(/\>/g, '\\\>');
  dateFormat=dateFormat.replace(/\|/g, '\\\|');
  dateFormat=dateFormat.replace(/\*/g, '\\\*');
  dateFormat=dateFormat.replace(/\?/g, '\\\?');
  dateFormat=dateFormat.replace(/\+/g, '\\\+');
  dateFormat=dateFormat.replace(/\^/g, '\\\^');
  dateFormat=dateFormat.replace(/\$/g, '\\\$');

  dateFormat=dateFormat.replace(/dd/gi, '\\d\\d');
  dateFormat=dateFormat.replace(/mm/gi, '\\d\\d');
  dateFormat=dateFormat.replace(/yyyy/gi, '\\d\\d\\d\\d');
  dateFormat=dateFormat.replace(/yy/gi, '\\d\\d');
  dateFormat=dateFormat.replace(/day/gi, '\\w\\w\\w');
  dateFormat=dateFormat.replace(/mon/gi, '\\w\\w\\w');

  dateFormat=dateFormat.replace(/hr/gi, '\\d\\d');
  dateFormat=dateFormat.replace(/mi/gi, '\\d\\d');
  dateFormat=dateFormat.replace(/ss/gi, '\\d\\d');
  dateFormat=dateFormat.replace(/am/gi, '\\w\\w');

  return dateFormat;
}

function xcGetDateFormatRE(f) {
  if (xcDateFormatRE==null) {
    xcDateFormatRE=new RegExp('^'+_xcGetDateFormatRE(f)+'$');
  }
  return xcDateFormatRE;
}

function xcGetYMDFromDate(date,dateFormat) {
  var y,m,d, _dateFormat=dateFormat||xcDateFormat;

  var yIdx=_dateFormat.search(/yyyy/i);
  var mIdx=_dateFormat.search(/mm/i);
  var m3Idx=_dateFormat.search(/mon/i);
  var dIdx=_dateFormat.search(/dd/i);

  y=date.substring(yIdx,yIdx+4)-0;
  if (mIdx!=-1) {
    m=date.substring(mIdx,mIdx+2)-1;
  }
  else {
    m=xcMonthFromName(date.substring(m3Idx,m3Idx+3))-0;
  }
  d=date.substring(dIdx,dIdx+2)-0;

  return new Array(y,m,d);
}

function xcGetDateFromYMD(y,m,d,dateFormat) {
  var _dateFormat=dateFormat||xcDateFormat;
  _dateFormat=_dateFormat.replace(/yyyy/i,y);
  _dateFormat=_dateFormat.replace(/mm/i,xcGetDD(m+1));
  _dateFormat=_dateFormat.replace(/MON/,xcMonthShortNames[m].toUpperCase());
  _dateFormat=_dateFormat.replace(/mon/i,xcMonthShortNames[m]);
  _dateFormat=_dateFormat.replace(/dd/i,xcGetDD(d));
  _dateFormat=_dateFormat.replace(/DAY/,xcDayShort(y,m,d).toUpperCase());
  _dateFormat=_dateFormat.replace(/day/i,xcDayShort(y,m,d));

  return _dateFormat;
}

function xcTransformDate(date,dateFormat1,dateFormat2) {
  if (date=="") { return ""; }
  var nums=xcGetYMDFromDate(date,dateFormat1);
  return xcGetDateFromYMD(nums[0],nums[1],nums[2],dateFormat2);
}

function xcGetYearString(y) {
  var ystr=y+"";
  return xcYearDigits[ystr.charAt(0)]+xcYearDigits[ystr.charAt(1)]+xcYearDigits[ystr.charAt(2)]+xcYearDigits[ystr.charAt(3)];
}

// calendar obj functions
function xcCalOBJ(idx) {
  this.idx=idx;
  this.targetField=null; this.refField=null;
  this.defaultDate="";
  this.holderId="";
  this.dx=0; this.dy=0;
  this.mode=1; // 0:static, 1:popup
  this.name="";
  this.timer=0;

  this.year=0; this.month=0; this.date=""; this.vacant=false; this.lastDate="";
  this.cellYear=0; this.cellMonth=0; this.cellDate=0; this.cellDay=0; this.cellWeek=0;

  this.div=null;
  this.holder=xcMakeLayer();
  this.holder.cal=this;
  if (xcIsIE55up && !xcIsMac) {
    this.holder.onmouseenter=xcResetAutoHide;
    this.holder.onmouseleave=xcSetAutoHide;
  }
  else {
    this.holder.onmouseover=xcResetAutoHide;
    this.holder.onmouseout=xcSetAutoHide;
  }

  this.conf={}; // for mod settings
  this.getConf=xcGetConf;

  this.show=xcShowCal; this.beforeShow=xcBeforeShow; this.afterShow=xcAfterShow;
  this.checkDateRange=xcCheckDateRange; this.dateOff=xcDateOff; this.dateOff2=xcDateOff2; this.specialDate=xcSpecialDate;
  this.mouseoverEvent=xcMouseOverEvent; this.mouseoutEvent=xcMouseOutEvent; this.clickEvent=xcMouseClickEvent;

  this.reset=xcResetCal; this.release=xcReleaseCal; this.cleanUp=xcCleanUp;

  this.get=xcGet; this.beforeGet=xcBeforeGet; this.afterGet=xcAfterGet;
  this.update=xcUpdate; this.beforeUpdate=xcBeforeUpdate; this.afterUpdate=xcAfterUpdate;

  this.scroll=xcScrollCal; this.beforeScroll=xcBeforeScroll; this.afterScroll=xcAfterScroll;

  this.calOpen=xcCalOpen; this.calClose=xcCalClose; this.calWeekdays=xcCalWeekdays();

  this.calHeader=xcCalHeader; this.calTitle=xcCalTitle; this.calMonthYear=xcCalMonthYear;
  this.calBody=xcCalBody; this.calCell=xcCalCell; this.dayCell=xcDayCell;
  this.calFooter=xcCalFooter; this.calFootBar=xcCalFootBar;
  this.addOn1=xcAddOn1; this.addOn2=xcAddOn2;
}

function xcGetConf(modKey,attrKey) {
  return typeof(this.conf[modKey])!="undefined"?this.conf[modKey][attrKey]||null:null;
}

function xcDateOff() { return false; }
function xcDateOff2(date) { return false; }
function xcSpecialDate() {
  var hasSpecial=null;
  for (var i=0; i<xcSpecialCalls.length; i++) {
    hasSpecial=xcSpecialCalls[i](this);
    if (hasSpecial) {
      break;
    }
  }
  return hasSpecial;
}
function xcCheckDateRange(dir) {}

function xcMouseOverEvent() { return ""; }
function xcMouseOutEvent() { return ""; }
function xcMouseClickEvent() { return ["",1]; }

function xcBeforeShow() {}
function xcAfterShow() {}
function xcShowCal() {
  var dx=this.dx, dy=this.dy, l=xcGetHolder(this.holderId);
  var currentDate="", popupDate=null, calRE=xcGetDateFormatRE(), calDate="";

  if (l!=null) {
    dx+=xcGetLayerX(l); dy+=xcGetLayerY(l);
  }
  else {
    dx+=xcMouseX; dy+=xcMouseY;
  }

  this.beforeGet();
  calDate=this.afterGet(this.get()||this.defaultDate);
  if (this.lastDate=="") {
    this.lastDate=calDate?calDate:getCurrentDate();
  }

  if (calDate!="" && calRE.test(calDate)) {
    var refDate=xcGetYMDFromDate(calDate);
    popupDate=new Date(refDate[0],refDate[1],refDate[2]);
  }
  else {
    popupDate=new Date();
  }
  currentDate=xcGetInternalDate(popupDate.getFullYear(),popupDate.getMonth(),popupDate.getDate());

  this.year=popupDate.getFullYear();
  this.month=popupDate.getMonth();

  this.checkDateRange(0);

  if (this.year!=popupDate.getFullYear() || this.month!=popupDate.getMonth()) {
    popupDate=new Date(this.year,this.month,1);
    currentDate="";
  }

  var cc=this.calHeader()+this.calBody(currentDate)+this.calFooter();
  if (this.mode==2) {
    this.div=l;
    l.innerHTML=cc;
  }
  else {
    var h=this.holder;
    xcMoveLayerTo(h,dx,dy);
    xcWriteLayer(h,cc);

    h.style.zIndex=++xcBaseZIndex;

    if (h.iframe) {
      if (!h.iframe.sized) {
        h.iframe.style.width=h.offsetWidth+"px";
        h.iframe.style.height=h.offsetHeight+"px";
        h.iframe.sized=true;
      }
      xcMoveLayerTo(h.iframe,dx,dy);
      h.iframe.style.zIndex=h.style.zIndex-1;
    }

    this.beforeShow();
    if (h.iframe) {
      xcShowLayer(h.iframe);
    }
    xcShowLayer(h);
    this.afterShow();
  }
}

function xcResetCal() {
  this.cleanUp();

  this.targetField=null; this.refField=null;
  this.defaultDate="";
  this.holderId="";
  this.dx=0; this.dy=0;
  this.mode=1;

  this.year=0; this.month=0; this.date=""; this.vacant=false; this.lastDate="";
  this.cellYear=0; this.cellMonth=0; this.cellDate=0; this.cellDay=0; this.cellWeek=0;
  this.conf={};
}
function xcReleaseCal() {
  this.cleanUp();
  this.vacant=true;
}
function xcCleanUp() {
  if (this.timer) {
    clearTimeout(this.timer);
    this.timer=0;
  }
}

function xcBeforeScroll() {}
function xcAfterScroll() {}
function xcScrollCal() {
  var currentDate="", actualDate=null, calRE=xcGetDateFormatRE(), calDate="";

  this.beforeGet();
  calDate=this.afterGet(this.get()||this.lastDate||this.defaultDate);

  if (calDate!="" && calRE.test(calDate)) {
    var refDate=xcGetYMDFromDate(calDate);
    actualDate=new Date(refDate[0],refDate[1],refDate[2]);
  }
  else {
    actualDate=new Date();
  }
  currentDate=xcGetInternalDate(actualDate.getFullYear(),actualDate.getMonth(),actualDate.getDate());

  this.beforeScroll();

  var cc=this.calHeader()+this.calBody(currentDate)+this.calFooter();
  if (this.mode==2) {
    this.div.innerHTML=cc;
  }
  else {
    xcWriteLayer(this.holder,cc);
    xcShowLayer(this.holder);
  }

  this.afterScroll();
}

function xcBeforeGet() { beforeGetDateValue(this.refField,this.targetField,this.idx); }
function xcAfterGet(date) { return afterGetDateValue(this.refField,this.targetField,date,this.idx); }

function xcGetField(field) { return field?getDateValue(field):""; }
function xcGet() { return xcGetField(this.refField)||xcGetField(this.targetField); }

function xcBeforeUpdate(date) { return beforeSetDateValue(this.refField,this.targetField,date,this.idx); }
function xcAfterUpdate(date) { afterSetDateValue(this.refField,this.targetField,date,this.idx); }

function xcUpdate(date) { if (this.targetField) { setDateValue(this.targetField,date); } }

// calendar layout functions
function xcDayCell(style,content,width,events) { return xcTDDIV(style,content,width,events); }

function xcAddOn1() { return ""; }
function xcAddOn2() { return ""; }

function xcCalOpen() { return xcTableOpen0+xcTROpen+"<TD>"+this.addOn1()+xcDIVOpen(xcCSSPanel)+(xcIsIEMac?xcTableOpen0+xcTROpen+"<TD>":""); }
function xcCalClose() { return (xcIsIEMac?"</TD>"+xcTRClose+xcTableClose:"")+xcDIVClose+this.addOn2()+"</TD>"+xcTRClose+xcTableClose; }

function xcSetAutoHide() {
  if (xcAutoHide && this.cal.mode==1) {
    this.cal.timer=setTimeout("xcHideCal("+this.cal.idx+")",xcAutoHide);
  }
}
function xcResetAutoHide() {
  if (xcAutoHide) {
    this.cal.cleanUp();
  }
}

function xcMouseEvents(cssOver,cssDown,cssOut,eventOver,eventClick,eventOut) {
  var s="";

  if (cssOver || eventOver) {
    s+=(xcIsIE55up && !xcIsMac)?" onmouseenter='":" onmouseover='";
    if (cssOver) {
      s+=xcCSSOpen+cssOver+xcCSSClose+";";
    }
    if (eventOver) {
      s+=eventOver;
    }
    s+="' ";
  }

  if (cssDown) {
    s+=" onmousedown='"+xcCSSOpen+cssDown+xcCSSClose+"' ";
  }
  if (eventClick) {
    s+=" onclick='"+eventClick+"' ";
  }

  if (cssOut || eventOut) {
    s+=(xcIsIE55up && !xcIsMac)?" onmouseleave='":" onmouseout='";
    if (cssOut) {
      s+=xcCSSOpen+cssOut+xcCSSClose+";";
    }
    if (cssOut) {
      s+=eventOut;
    }
    s+="' ";
  }

  return s;
}

function xcCalArrows(idx) {
  var prevYear="xcMoveYear("+idx+",-1)", nextYear="xcMoveYear("+idx+",1)";
  var prevMonth="xcMoveMonth("+idx+",-1)", nextMonth="xcMoveMonth("+idx+",1)";

  var arrowsPrev="", arrowsNext="";
  if (xcArrowSwitch[0]==1) {
    var arrowYearPrev=xcCSSArrowYearPrev, arrowYearNext=xcCSSArrowYearNext;
    arrowsPrev=xcTDDIV(arrowYearPrev[0],xcArrowYear[0],"",xcMouseEvents(arrowYearPrev[1],arrowYearPrev[2],arrowYearPrev[0],"",prevYear,""));
    arrowsNext=xcTDDIV(arrowYearNext[0],xcArrowYear[1],"",xcMouseEvents(arrowYearNext[1],arrowYearNext[2],arrowYearNext[0],"",nextYear,""));
  }
  if (xcArrowSwitch[1]==1) {
    var arrowMonthPrev=xcCSSArrowMonthPrev, arrowMonthNext=xcCSSArrowMonthNext;
    arrowsPrev+=xcTDDIV(arrowMonthPrev[0],xcArrowMonth[0],"",xcMouseEvents(arrowMonthPrev[1],arrowMonthPrev[2],arrowMonthPrev[0],"",prevMonth,""));
    arrowsNext=xcTDDIV(arrowMonthNext[0],xcArrowMonth[1],"",xcMouseEvents(arrowMonthNext[1],arrowMonthNext[2],arrowMonthNext[0],"",nextMonth,""))+arrowsNext;
  }

  return [arrowsPrev,arrowsNext];
}

function xcCalMonthYear() {
  var yy=xcYearPrefix+xcGetYearString(this.year)+xcYearSuffix, mm=xcMonthPrefix+xcMonthNames[this.month]+xcMonthSuffix;
  return xcTDDIV(xcCSSHead,xcHeadTagOrder==1?mm+xcHeadSeparator+yy:xcHeadTagOrder==2?mm:yy+xcHeadSeparator+mm,xcHeadTagAdjustment==1?"100%":"");
}

function xcCalTitle() {
  var arrows=["", ""];

  if (xcArrowPosition==0) {
    arrows=xcCalArrows(this.idx);
  }

  s=xcDIVOpen(xcCSSHeadBlock)+(xcHeadTagAdjustment==1?xcTableOpen2:xcTableOpen1)+xcTROpen2;
  s+=arrows[0];
  s+=this.calMonthYear();
  s+=arrows[1];
  s+=xcTRClose+xcTableClose+xcDIVClose;

  return s;
}

function xcCalWeekdays() {
  var s=xcDIVOpen(xcCSSWeekdayBlock)+xcTableOpen3+xcTROpen;
  for (var i=xcWeekStart; i<xcWeekStart+7; i++) {
    s+=xcTDDIV(xcCSSWeekday,xcWeekdayDisplay[i],"");
  }
  s+=xcTRClose+xcTableClose+xcDIVClose;

  return s;
}

function xcCalHeader() { return this.calOpen()+this.calTitle()+this.calWeekdays; }

function xcCalCell(mode,currentDate) {
  var s="", idx=this.idx;

  if (mode==xcRCell) {
    var calCopyright='this.title="'+xcCalCopyright+'"';
    var calHome='window.open("'+xcCalHome+'")';
    s=this.dayCell(_dayNormalCurrent["on"],xcCopyrightDisplay,"",xcMouseEvents(_dayNormalCurrent["over"],_dayNormalCurrent["down"],_dayNormalCurrent["on"],calCopyright,calHome,""));
  }
  else if (mode==xcECell) {
    s=this.dayCell(xcCSSDayEmpty,xcDayContents[0],"");
  }
  else {
    var dateOff=this.dateOff(), cssDateSpecial=this.specialDate();
    var eventOver=this.mouseoverEvent(), eventOut=this.mouseoutEvent(), _eventClick=this.clickEvent();
    var eventClick=_eventClick[0]+(_eventClick[1]?"xcPickDate("+idx+",\""+this.date+"\");":"");
    var ca=null, cb=null, cc=null, cd=null;
    var today=new Date();
    var todayDate=xcGetInternalDate(today.getFullYear(),today.getMonth(),today.getDate());

    if (dateOff) {
      cd=mode==xcCCell?_dayNormal:_dayOther;

      if (cssDateSpecial) {
        ca=cssDateSpecial[1].split(":");
        cb=mode==xcCCell?ca[0]:ca[1];
        cc=mode==xcCCell?_dayNormalSpecial:_dayOtherSpecial;
        s=this.dayCell(cb||cc["off"]||cd["off"]||_dayNormal["off"],xcDayContentsDisabled[this.cellDate],"");
      }
      else {
        s=this.dayCell(cd["off"]||_dayNormal["off"],xcDayContentsDisabled[this.cellDate],"");
      }
    }
    else if (currentDate==this.date && xcShowCurrentDate==1 || todayDate==this.date && xcShowCurrentDate==2) {
      cc=mode==xcCCell?_dayNormalCurrent:_dayOtherCurrent;
      cd=_dayNormalCurrent;
      s=this.dayCell(cc["on"]||cd["on"],xcDayContentsCurrent[this.cellDate],"",xcMouseEvents(cc["over"]||cd["over"],cc["down"]||cd["down"],cc["on"]||cd["on"],eventOver,eventClick,eventOut));
    }
    else if (cssDateSpecial) {
      ca=cssDateSpecial[0].split(":");
      cb=mode==xcCCell?ca[0]:ca[1];
      cc=mode==xcCCell?_dayNormalSpecial:_dayOtherSpecial;
      cd=mode==xcCCell?_dayNormal:_dayOther;
      s=this.dayCell(cb||cc["on"]||cd["on"],xcDayContents[this.cellDate],"",xcMouseEvents(cc["over"]||cd["over"],cc["down"]||cd["down"],cb||cc["on"]||cd["on"],eventOver,eventClick,eventOut));
    }
    else {
      cc=mode==xcCCell?_dayNormal:_dayOther;
      cd=_dayNormal;
      s=this.dayCell(cc["on"]||cd["on"],xcDayContents[this.cellDate],"",xcMouseEvents(cc["over"]||cd["over"],cc["down"]||cd["down"],cc["on"]||cd["on"],eventOver,eventClick,eventOut));
    }
  }

  return s;
}

function xcCalBody(currentDate) {
  var dayOffset=0, dayCount=1, firstDay=(new Date(this.year,this.month,1)).getDay();
  var thisMonth=new Date(this.year,this.month+1,0), lastDay=thisMonth.getDate();
  var prevMonth=new Date(this.year,this.month,0), prevMonthYear=prevMonth.getFullYear(), prevMonthMonth=prevMonth.getMonth(), prevLastDay=prevMonth.getDate();
  var nextMonth=new Date(this.year,this.month+1,1), nextMonthYear=nextMonth.getFullYear(), nextMonthMonth=nextMonth.getMonth();

  if (xcWeekStart>0 && firstDay==0) { firstDay=7; }

  var s=xcDIVOpen(xcCSSDayBlock)+xcTableOpen3;
  for (var i=0; i<6; i++) {
    this.cellWeek=i;

    s+=xcTROpen;
    for (var j=xcWeekStart; j<xcWeekStart+7; j++) {
      this.cellDay=j; this.date="";

      if (i==5 && j==xcWeekStart+6 && xcShowCopyright) {
        s+=this.calCell(xcRCell);
      }
      else if (i*7+j<firstDay || dayCount>lastDay) {
        if (xcShowPrevNextMonth && i*7+j<firstDay) {
          dayOffset=i*7+j-firstDay+1;
          this.cellYear=prevMonthYear;
          this.cellMonth=prevMonthMonth;
          this.cellDate=prevLastDay+dayOffset;
          this.date=xcGetInternalDate(this.cellYear,this.cellMonth,this.cellDate);
          s+=this.calCell(xcOCell,currentDate);
        }
        else if (xcShowPrevNextMonth && dayCount>lastDay) {
          this.cellYear=nextMonthYear;
          this.cellMonth=nextMonthMonth;
          this.cellDate=(dayCount++)-lastDay;
          this.date=xcGetInternalDate(this.cellYear,this.cellMonth,this.cellDate);
          s+=this.calCell(xcOCell,currentDate);
        }
        else {
          s+=this.calCell(xcECell);
        }
      }
      else {
        this.cellYear=this.year;
        this.cellMonth=this.month;
        this.cellDate=dayCount++;
        this.date=xcGetInternalDate(this.cellYear,this.cellMonth,this.cellDate);
        s+=this.calCell(xcCCell,currentDate);
      }
    }
    s+=xcTRClose;
  }
  s+=xcTableClose+xcDIVClose;

  return s;
}

function xcCalFootBar() {
  var s="", footSwitch=0, footToday=xcCSSFootToday, footClear=xcCSSFootClear, footBack=xcCSSFootBack, footClose=xcCSSFootClose, footReset=xcCSSFootReset;

  var footTagOrders=xcFootTagSwitch.concat(xcFootButtonSwitch);
  for (var i=0; i<footTagOrders.length; i++) {
    if (footTagOrders[i]) {
      footSwitch++;
    }
  }

  if (footSwitch>0) {
    var idx=this.idx, today=getTooltipDate(new Date()), defaultDate=getTooltipDate(toJSDate(this.defaultDate)), lastDate=getTooltipDate(toJSDate(this.lastDate));
    var todayLink="xcPickDate("+idx+",\""+xcTransformDate(getCurrentDate(),xcDateFormat,xcInternalDateFormat)+"\")",
        clearLink="xcClearDate("+idx+")",
        backLink="xcScrollToLastDate("+idx+")",
        closeLink="xcHideCal("+idx+")",
        resetLink="xcResetDefaultDate("+idx+")";
    var _footLinks=[{"order":footTagOrders[0], "display":xcFootTags[0], "call":todayLink, "tooltip":xc_Today_is+today, "cssout":footToday[0], "cssover":footToday[1], "cssdown":footToday[2]},
                    {"order":footTagOrders[1], "display":xcFootTags[1], "call":clearLink, "tooltip":xc_Clear_the_date_input, "cssout":footClear[0], "cssover":footClear[1], "cssdown":footClear[2]},
                    {"order":footTagOrders[2], "display":xcFootTags[2], "call":backLink, "tooltip":xc_Scroll_to+lastDate, "cssout":footBack[0], "cssover":footBack[1], "cssdown":footBack[2]},
                    {"order":footTagOrders[3], "display":xcFootTags[3], "call":closeLink, "tooltip":xc_Close_the_calendar, "cssout":footClose[0], "cssover":footClose[1], "cssdown":footClose[2]},
                    {"order":footTagOrders[4], "display":xcFootTags[4], "call":resetLink, "tooltip":xc_Pick_the_default_date_of+defaultDate, "cssout":footReset[0], "cssover":footReset[1], "cssdown":footReset[2]},
                    {"order":footTagOrders[5], "display":today, "call":todayLink, "tooltip":xc_Today, "cssout":footToday[0], "cssover":footToday[1], "cssdown":footToday[2]},
                    {"order":footTagOrders[6], "display":lastDate, "call":backLink, "tooltip":xc_Scroll_to_this_date, "cssout":footBack[0], "cssover":footBack[1], "cssdown":footBack[2]},
                    {"order":footTagOrders[7], "display":defaultDate, "call":resetLink, "tooltip":xc_Pick_the_default_date, "cssout":footReset[0], "cssover":footReset[1], "cssdown":footReset[2]}
                   ];
    for (var i=0; i<xcFootButtonSwitch.length;i++) {
      _footLinks[_footLinks.length] = {"order":xcFootButtonSwitch[i], "display":typeof(xcFootButtons[i])=="function"?xcFootButtons[i](this.targetField,this.refField,this.defaultDate,this.lastDate,this.idx):xcFootButtons[i], "call":xcFootButtonLinks[i]==null?"":"xcFootLinkLoader("+idx+","+i+")", "tooltip":"", "cssout":xcCSSFootOther[i][0], "cssover":xcCSSFootOther[i][1], "cssdown":xcCSSFootOther[i][2]}
    }
    var footLinks=_footLinks.sort(xcSort);

    var arrows=["", ""];
    if (xcArrowPosition==1) {
      arrows=xcCalArrows(idx);
    }

    w=xcFootTagAdjustment==1?xcFootTagWidth[footSwitch>5?5:footSwitch]:"";

    s+=xcDIVOpen(xcCSSFootBlock)+(xcFootTagAdjustment==0?xcTableOpen1:xcTableOpen2)+xcTROpen2;
    s+=arrows[0];
    for (var i=0; i<footLinks.length; i++) {
      if (footLinks[i].order!=0) {
        s+=xcTDDIV(footLinks[i].cssout,footLinks[i].display,w,xcMouseEvents(footLinks[i].cssover,footLinks[i].cssdown,footLinks[i].cssout,"",footLinks[i].call,""),footLinks[i].tooltip);
      }
    }
    s+=arrows[1];
    s+=xcTRClose+xcTableClose+xcDIVClose;
  }

  return s;
}

function xcCalFooter() { return this.calFootBar()+this.calClose(); }

// calendar popup functions
function xcMoveYear(idx,dy) {
  var curCal=xcFindCal(idx);
  curCal.year+=dy;

  curCal.checkDateRange(dy);

  beforeScrollCalendar(curCal.name,xcGetDD(curCal.year),xcGetDD(curCal.month+1));
  curCal.scroll();
  afterScrollCalendar(curCal.name,xcGetDD(curCal.year),xcGetDD(curCal.month+1));
}

function xcMoveMonth(idx,dm) {
  var curCal=xcFindCal(idx);
  curCal.month+=dm;
  while (curCal.month<0) { curCal.month+=12; curCal.year--; }
  while (curCal.month>11) { curCal.month-=12; curCal.year++;}

  curCal.checkDateRange(dm);

  beforeScrollCalendar(curCal.name,xcGetDD(curCal.year),xcGetDD(curCal.month+1));
  curCal.scroll();
  afterScrollCalendar(curCal.name,xcGetDD(curCal.year),xcGetDD(curCal.month+1));
}

function xcClearDate(idx) {
  var curCal=xcFindCal(idx);

  curCal.beforeUpdate(""); curCal.update(""); curCal.afterUpdate("");
  curCal.lastDate="";

  if (curCal.mode==1 && !xcStickyMode) {
    xcHideCal(idx);
  }
  else {
    curCal.scroll();
  }
}

function xcPickDate(idx,date) {
  var curCal=xcFindCal(idx);

  var dateYMD=xcGetYMDFromDate(date,xcInternalDateFormat);
  curCal.year=dateYMD[0]; curCal.month=dateYMD[1];

  if (curCal.dateOff2(date)) {
    curCal.scroll();
    return;
  }

  var calDate=curCal.beforeUpdate(xcTransformDate(date,xcInternalDateFormat,xcDateFormat));
  curCal.update(calDate);
  curCal.afterUpdate(calDate);

  curCal.lastDate=calDate;

  if (curCal.mode==1 && !xcStickyMode) {
    xcHideCal(idx);
  }
  else {
    curCal.scroll();
  }
}

function xcScrollToLastDate(idx) {
  var curCal=xcFindCal(idx), d=xcGetYMDFromDate(curCal.lastDate||getCurrentDate());
  curCal.year=d[0]; curCal.month=d[1];
  curCal.scroll();
}

function xcResetDefaultDate(idx) {
  var curCal=xcFindCal(idx), d=xcGetYMDFromDate(xcGetDateFormatRE().test(curCal.defaultDate)?curCal.defaultDate:getCurrentDate());
  curCal.year=d[0]; curCal.month=d[1];
  xcPickDate(idx,xcGetInternalDate(d[0],d[1],d[2]));
}

function xcHideCal(idx) {
  var curCal=xcFindCal(idx), h=curCal.holder;

  if (curCal.mode==1) {
    if (h.iframe) { xcHideLayer(h.iframe); }
    xcHideLayer(h);
    curCal.release();
  }
}

function xcFootLinkLoader(idx,i) {
  var curCal=xcFindCal(idx), footLink=xcFootButtonLinks[i];

  footLink(curCal.targetField, curCal.refField, curCal.defaultDate, curCal.lastDate, idx);

  if (curCal.mode==1 && !xcStickyMode) {
    xcHideCal(idx);
  }
  else {
    curCal.scroll();
  }
}

function xcGetCalendar(name) {
  for (var i=0; i< xcCalPool.length; i++) {
    if (!xcCalPool[i].vacant && xcCalPool[i].name==name) {
      return xcCalPool[i];
    }
  }
  return null;
}

// user functions
function showCalendar(refKey,targetField,refField,defaultDate,holderId,dx,dy,mode,name) {
  if (!xcCalSafe) { return; }

  if (!xcOnResizeChecked) {
    xcOnResizeChecked=1;
    xcOtherResize=window.onresize?window.onresize:xcVoid; window.onresize=xcResizeCheck;
  }

  var curCal=xcGetCal(refKey,targetField,refField,defaultDate,holderId,dx,dy,mode,name);
  curCal.show();

  return curCal.name;
}
function hideCalendars() {
  for (var i=0; i<xcCalPool.length; i++) {
    if (!xcCalPool[i].vacant && xcCalPool[i].mode==1) {
      xcCalPool[i].reset();
      xcHideCal(i);
    }
  }
}

function toCalendarDate(date) { return xcGetDateFromYMD(date.getFullYear(),date.getMonth(),date.getDate()); }; var toCalDate=toCalendarDate;
function toJSDate(date) {
  var calRE=xcGetDateFormatRE();

  if (calRE.test(date)) {
    var d=xcGetYMDFromDate(date);
    return (new Date(d[0],d[1],d[2]));
  }
  else {
    return (new Date());
  }
}
function getCurrentDate() { return toCalendarDate(new Date()); }
function getTooltipDate(date) {
  return date.getFullYear()+"-"+xcGetDD(date.getMonth()+1)+"-"+xcGetDD(date.getDate());
}

function checkDate(date) {
  if (date) {
    var calRE=xcGetDateFormatRE();

    if (calRE.test(date)) {
      return 0;
    }
    else {
      return 1;
    }
  }
  else {
    return 2;
  }
}

function compareDates(date1,date2) {
  var calRE=xcGetDateFormatRE();
  var d1=getDateNumbers(calRE.test(date1)?date1:getCurrentDate()).join("");
  var d2=getDateNumbers(calRE.test(date2)?date2:getCurrentDate()).join("");

  return (d1==d2?0:d1>d2?1:-1);
}

function getDateNumbers(date) {
  var calRE=xcGetDateFormatRE();

  if (calRE.test(date)) {
    var d=xcGetYMDFromDate(date);
    return new Array(xcGetDD(d[0]),xcGetDD(d[1]+1),xcGetDD(d[2]));
  }
  else {
    return new Array("","","");
  }
}; var getNumbers=getDateNumbers;

function hideCalendar(name) {
  var curCal=xcGetCalendar(name);
  if (curCal!=null) {
    xcHideCal(curCal.idx);
  }
}

function getCalendarDate(name) {
  var curCal=xcGetCalendar(name);
  if (curCal!=null) {
    return curCal.lastDate;
  }
  else {
    return "";
  }
}

function setCalendarDate(name,date) {
  var nums=getDateNumbers(date);
  var curCal=xcGetCalendar(name);
  if (curCal!=null && nums[0]!="") {
    curCal.year=nums[0]-0;
    curCal.month=nums[1]-1;
    curCal.scroll();
  }
}

function refreshCalendar(name) {
  var curCal=xcGetCalendar(name);
  if (curCal!=null) {
  	curCal.scroll();
  }
}

function scrollMonth(name,dm) {
  var curCal=xcGetCalendar(name);
  if (curCal!=null) {
    xcMoveMonth(curCal.idx,dm);
  }
}

function scrollYear(name,dy) {
  var curCal=xcGetCalendar(name);
  if (curCal!=null) {
    xcMoveYear(curCal.idx,dy);
  }
}

// user overload functions
function beforeGetDateValue(refField,targetField,idx) {}
function afterGetDateValue(refField,targetField,date,idx) { return date; }
function getDateValue(field) { return field.value; }
function beforeSetDateValue(refField,targetField,date,idx) { return date; }
function afterSetDateValue(refField,targetField,date,idx) {}
function setDateValue(field,date) { field.value=date; }
function beforeScrollCalendar(name,year,month) {}
function afterScrollCalendar(name,year,month) {}

// register mousemove event
function xcRegisterMouseMove() {
  if (xcIsN6) { document.captureEvents(Event.MOUSEMOVE); }
  if (document.onmousemove) { xcOldMouseMove=document.onmousemove; }
  document.onmousemove=xcTrackMouseMove;
}

if (xcCalSafe) {
  xcSetStyles();
  xcRegisterMouseMove();

  var modList=xcMods.sort(xcSort);
  for (var i=0; i<modList.length; i++) {
    if (modList[i].order!=0) {
      document.write("<scr"+"ipt language='javascript' src='"+xcModPath+modList[i].script+"' type='text/javascript'><\/scr"+"ipt>");
    }
  }
}

function xcSetStyles() {
  var dayNormal=xcCSSDay, dayCurrent=xcCSSDayCurrent, dayOther=xcCSSDayOther, daySpecial=xcCSSDaySpecial;

  _dayNormal["on"]=dayNormal[0];
  _dayNormal["over"]=dayNormal[1];
  _dayNormal["down"]=dayNormal[2];
  _dayNormal["off"]=dayNormal[3];

  _dayNormalCurrent["on"]=dayCurrent[0];
  _dayNormalCurrent["over"]=dayCurrent[1];
  _dayNormalCurrent["down"]=dayCurrent[2];

  if (typeof(xcCSSVersion)=="undefined") {
    _dayNormalSpecial["on"]=daySpecial[0];
    _dayNormalSpecial["over"]="";
    _dayNormalSpecial["down"]="";
    _dayNormalSpecial["off"]=daySpecial[1];

    _dayOther["on"]=dayOther[0];
    _dayOther["over"]=dayNormal[1];
    _dayOther["down"]=dayNormal[2];
    _dayOther["off"]=dayOther[1];

    _dayOtherCurrent["on"]=dayCurrent[0];
    _dayOtherCurrent["over"]="";
    _dayOtherCurrent["down"]="";

    _dayOtherSpecial["on"]="";
    _dayOtherSpecial["over"]="";
    _dayOtherSpecial["down"]="";
    _dayOtherSpecial["off"]="";
  }
  else if (xcCSSVersion=="2.9") {
    var dayOtherCurrent=xcCSSDayOtherCurrent, dayOtherSpecial=xcCSSDayOtherSpecial;

    _dayNormalSpecial["on"]=daySpecial[0];
    _dayNormalSpecial["over"]=daySpecial[1];
    _dayNormalSpecial["down"]=daySpecial[2];
    _dayNormalSpecial["off"]=daySpecial[3];

    _dayOther["on"]=dayOther[0];
    _dayOther["over"]=dayOther[1];
    _dayOther["down"]=dayOther[2];
    _dayOther["off"]=dayOther[3];

    _dayOtherCurrent["on"]=dayOtherCurrent[0];
    _dayOtherCurrent["over"]=dayOtherCurrent[1];
    _dayOtherCurrent["down"]=dayOtherCurrent[2];

    _dayOtherSpecial["on"]=dayOtherSpecial[0];
    _dayOtherSpecial["over"]=dayOtherSpecial[1];
    _dayOtherSpecial["down"]=dayOtherSpecial[2];
    _dayOtherSpecial["off"]=dayOtherSpecial[3];
  }
}
