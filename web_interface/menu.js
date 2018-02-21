
<!--//--><![CDATA[//><!--

var ie4=document.all;
var ns6=document.getElementById&&!document.all;

function getposOffset(what, offsettype) {
        var totaloffset=(offsettype=="right")? what.offsetLeft : what.offsetTop;
        var parentEl=what.offsetParent;
        while (parentEl!=null){
                totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
                parentEl=parentEl.offsetParent;
        }
        return totaloffset;
}


function showhide(obj, e, visible, hidden, menuwidth) {
        if (ie4||ns6) {dropmenuobj.style.left=dropmenuobj.style.top=-500;}
        if (menuwidth!=""){
                dropmenuobj.widthobj=dropmenuobj.style
                dropmenuobj.widthobj.width=200;
        }
        if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover") {obj.visibility=visible;}
        else if (e.type=="click") {obj.visibility=hidden;}
}

function iecompattest(){
        return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
}

function clearbrowseredge(obj, whichedge){
        var edgeoffset=0;
        if (whichedge=="rightedge"){
                var windowedge=ie4 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset
+window.innerWidth-15;
                dropmenuobj.contentmeasure=dropmenuobj.offsetWidth;
                if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure) {
                        edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth;
                }
        } else {
                var windowedge=ie4 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset
+window.innerHeight-18;
                dropmenuobj.contentmeasure=dropmenuobj.offsetHeight;
                if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure) {
                        edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight;
                }
        }
        return edgeoffset;
}

function dropdownmenu(obj, e,doname) {
        hidemenu();
        if (window.event) {event.cancelBubble=true;}
        else if (e.stopPropagation) {e.stopPropagation();}
        clearhidemenu();
        dropmenuobj=document.getElementById? document.getElementById(doname) : doname;

        if (ie4||ns6){
                showhide(dropmenuobj.style, e, "visible", "hidden", 150);
                dropmenuobj.x=getposOffset(obj, "left");
                dropmenuobj.y=getposOffset(obj, "top");
                dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px";		
                dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight+"px";
        }

        return clickreturnvalue();
}

function clickreturnvalue(){
        if (ie4||ns6) {return false;} else {return true;}
}

function contains_ns6(a, b) {
        while (b.parentNode) {
                if ((b = b.parentNode) == a) {return true;}
        }
        return false;
}

function dynamichide(e){
        if (ie4&&!dropmenuobj.contains(e.toElement)) {
                delayhidemenu();
        } else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget)) {
                delayhidemenu();
        }
}

function hidemenu(e){
        if (typeof dropmenuobj!="undefined"){
                if (ie4||ns6) {
                        dropmenuobj.style.visibility="hidden";
                }
        }
}

function delayhidemenu(){
        if (ie4||ns6) {
                delayhide=setTimeout("hidemenu()",5);
        }
}

function clearhidemenu(){
        if (typeof delayhide!="undefined") {
                clearTimeout(delayhide);
        }
}


function light_on(obj) {
        obj.setAttribute('bgcolor', '#A3A3A3', 0);
}

function light_off(obj) {
        obj.setAttribute('bgcolor', 'black', 0);
}
//--><!]]>