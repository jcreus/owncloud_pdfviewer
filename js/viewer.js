viewer_is_shown = false;
function showPDFviewer(dir,filename){
	if(!viewer_is_shown){
                $("#editor").hide();
                var url = OC.filePath('files','ajax','download.php')+'?files='+encodeURIComponent(filename)+"&dir="+encodeURIComponent(dir);
                $.get(OC.filePath('files_pdfviewer','ajax','setlastopened.php')+'?f='+encodeURIComponent(filename)+"&d="+encodeURIComponent(dir));
                console.log(url);
                $('table').hide();
                function im(path) { return OC.filePath('files_pdfviewer','js','pdfjs/web/images/'+path); }
                $("#controls").empty()
                $("#controls").html('&nbsp;&nbsp;<div id="controls2" style="display:inline;">      <button id="previous" onclick="PDFView.page--;" oncontextmenu="return false;">        <img src="'+im('go-up.svg')+'" align="top" height="10"/>        Previous      </button>      <button id="next" onclick="PDFView.page++;" oncontextmenu="return false;">        <img src="'+im('go-down.svg')+'" align="top" height="10"/>        Next      </button>      <div class="separator"></div>      <input style="width:25px;" type="number" id="pageNumber" onchange="PDFView.page = this.value;" value="1" size="4" min="1" />      <span>/</span>      <span id="numPages">--</span>      <div class="separator"></div>      <button id="zoomOut" title="Zoom Out" onclick="PDFView.zoomOut();" oncontextmenu="return false;">        <img src="'+im('zoom-out.svg')+'" align="top" height="10"/>      </button>      <button id="zoomIn" title="Zoom In" onclick="PDFView.zoomIn();" oncontextmenu="return false;">        <img src="'+im('zoom-in.svg')+'" align="top" height="10"/>      </button>      <div class="separator"></div>      <select id="scaleSelect" onchange="PDFView.parseScale(this.value);" oncontextmenu="return false;">        <option id="customScaleOption" value="custom"></option>        <option value="0.5">50%</option>        <option value="0.75">75%</option>        <option value="1">100%</option>        <option value="1.25" selected="selected">125%</option>        <option value="1.5">150%</option>        <option value="2">200%</option>        <option id="pageWidthOption" value="page-width">Page Width</option>        <option id="pageFitOption" value="page-fit">Page Fit</option>      </select>      <div class="separator"></div>      <button id="print" onclick="window.print();" oncontextmenu="return false;">        <img src="'+im('document-print.svg')+'" align="top" height="10"/>        Print      </button>      <button id="download" title="Download" onclick="PDFView.download();" oncontextmenu="return false;">        <img src="'+im('download.svg')+'" align="top" height="10"/>        Download      </button>       <span id="info">--</span>    </div>');
                    $("#content").html($("#content").html()+'<div id="loading">Loading... 0%</div>    <div id="viewer"></div>');
                    $("#controls").css({top:"0px",height:"3.5em"});
                  
                  PDFView.open(url,1.00);
                  $("#pageWidthOption").attr("selected","selected");
                  $("header,#apps").css({opacity:0.25});
                  document.getElementsByTagName("nav")[0].style.background = "rgba(238,238,238,.50)";
                  $("header").css({zIndex:10000000});
                  viewer_is_shown = true;
	}
}

var extrahtml = '<li id="extra" style="display:none;"><a title="" href="'+OC.webroot+"/apps/files_pdfviewer/lastopened.php"+'" style="background-image:url(/owncloud/apps/files_pdfviewer/css/history.png)">Last opened</a></li>';

$(document).ready(function(){
     if(location.href.indexOf("files")!=-1) {
        PDFJS.workerSrc = OC.filePath('files_pdfviewer','js','pdfjs/build/pdf.js');
	if(typeof FileActions!=='undefined'){
		FileActions.register('application/pdf','Edit','',function(filename){
			showPDFviewer($('#dir').val(),filename);
		});
		FileActions.setDefault('application/pdf','Edit');
	}
        $("#navigation .active").after(extrahtml);
        $("#navigation .active").html("<div style=\"float:right;\">&#x25BC;</div>"+$("#navigation .active").html());
        $("#navigation .active, #extra").hover(function () {
          $("#extra").show();
        },function () {$("#extra").hide();});
	OC.search.customResults.Text=function(row,item){
		var text=item.link.substr(item.link.indexOf('file=')+5);
		var a=row.find('a');
		a.data('file',text);
		a.attr('href','#');
		a.click(function(){
			var file=text.split('/').pop();
			var dir=text.substr(0,text.length-file.length-1);
			showFileEditor(dir,file);
		});
	}
    }
});
