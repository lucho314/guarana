// CanvasPaint r1
// by Christopher Clay <canvas@c3o.org>
//
// The code in this file is dedicated to the Public Domain
// http://creativecommons.org/licenses/publicdomain/
//

var canvas, c, canvastemp, ctemp, canvassel, csel, wsp, co, check, m;
var prefs = { pretty:false, controlpoints:false }
var dashed = new Image();
dashed.src = 'images/dashed.gif';
var FILL_STYLE = 'white';
var drawingColor;
var undos = [];
var undosIndex = 0;

function setDrawingColor(color) {
	drawingColor = c.strokeStyle = color;
}

function initPaint() {
	wsp = document.getElementById('workspace');
	canvas = document.getElementById('canvas');
	c = canvas.getContext("2d");

	//set up defaults
	c.tool = new tool.arrow(); 
	c.fillStyle = FILL_STYLE;
	
	if (!drawingColor) {
		drawingColor = "#f44336";
	}
	
	setDrawingColor(drawingColor);
	c.tertStyle = '#DDD';
	c.strokeFill = 1; //outline shapes
	//prefs.pretty = document.getElementById('pretty').checked;
	//prefs.controlpoints = document.getElementById('controlpoints').checked;

	//set up overlay canvas (for preview when drawing lines, rects etc)
	canvastemp = document.getElementById("canvastemp");
	ctemp = canvastemp.getContext("2d");	

	//set up selection canvas (invisible, used for selections)
	canvassel = document.getElementById("canvassel");
	csel = canvassel.getContext("2d");

	//set up events
	window.onmouseup = bodyUp;
	window.onmousemove = bodyMove;
	window.onkeydown = shortcuts;
	canvas.onmousedown = canvastemp.onmousedown = c_down;
	canvas.onmousemove = canvastemp.onmousemove = c_move;
	canvas.onmouseout = canvastemp.onmouseout = c_out;
	canvas.onmouseup = canvastemp.onmouseup = c_up;
	
	$(canvas).add(canvastemp)
		.on("touchstart", function(e) {
			c_down(e.originalEvent);
			return false;
		})
		.on("touchmove", function(e) {
			c_move(e.originalEvent);
			e.preventDefault();
			return false;
		})
		.on("touchend", function(e) {
			c_up(e.originalEvent);
			return false;
		})
	;
}

function shortcuts(e) {
	if(e.keyCode == 46) { //delete
		if(c.tool.name == 'select' && c.tool.status > 0) { //del selection
			c.tool.del();
		}
	} else if (e.keyCode == 27) {
		$("#text").val("").hide();
	} else if (e.keyCode == 13) {
		//$("#text").blur();
		//document.getElementById("canvas").className = "text";
	} else if(e.ctrlKey || e.metaKey) {
		var letter = String.fromCharCode(e.keyCode);
		switch(letter) {
			case 'C':
				copy();
				break;
			case 'V':
		        paste();
				break;
			case 'X':
		        cut();
				break;
			case 'Z':
		        undo();
				break;
		}
	}
	return true;
}

function sel_cancel() {
	if (c.tool.status == 2) {
		if (ctemp && ctemp.start) {
			c.drawImage(canvassel, Math.floor(ctemp.start.x), Math.floor(ctemp.start.y));
		}
	}
	if (c.tool.status != 4) {
		canvastemp.style.display='none';
	}
}

function copy() {
  if(c.tool.name == 'select' && c.tool.status > 0) {  //copy selection
		c.tool.copy();
	}
}

function cut() {
	if(c.tool.name == 'select' && c.tool.status > 0) {  //cut selection
		c.tool.copy();
		c.tool.del();
	}
}

function paste() {
	c.tool.paste();
}

function undoSave(firstTime) {
	console.log("undosave");
	
	// remove all redos
	console.log("index: " + undosIndex + " " + undos.length);
	if (undosIndex < undos.length-1) {
		console.log("splice");
		undos.splice(undosIndex+1, 999);
	}
	undos.push(cloneCanvas(canvas));
	
	if (!firstTime) {
		undosIndex++;
	}
	
	$("#undo").removeAttr("disabled");
	$("#redo").attr("disabled", "");
}

function undo() {
	if (undosIndex != 0) {
		// i haven't been able to restore canvas size on crop undo - so let's just restart 
		if (undosIndex == 1 && c.tool.name == "select") {
			$("#refresh").click();
		} else {
			// patch seems we need to reset globalAlpha or else the undo didn't work
			c.globalAlpha = 1;
			
			undosIndex--;
			
			console.log("undo() " + undosIndex);
			
			if (c.tool.name == 'select') {	//reset all info about current selection
				activateTempCanvas(); 
				canvastemp.style.display='none';
				c.tool.status = 0;
			}

			c.scale(1 / devicePixelRatio, 1 / devicePixelRatio);
			c.drawImage(undos[undosIndex], 0, 0);
			c.scale(devicePixelRatio, devicePixelRatio);
			
			if (undosIndex == 0) {
				$("#undo").attr("disabled", "");
			}
			if (undosIndex < undos.length) {
				$("#redo").removeAttr("disabled");
			}
			$("#redo").css("visibility", "visible");		
		}
	}
}

function redo() {
	undosIndex++;
	console.log("redo: " + undosIndex);
	
	c.scale(1 / devicePixelRatio, 1 / devicePixelRatio);
	c.drawImage(undos[undosIndex], 0, 0);
	c.scale(devicePixelRatio, devicePixelRatio);
	
	$("#undo").removeAttr("disabled");
	if (undosIndex >= undos.length-1) {
		$("#redo").attr("disabled", "");
	}
}

function getxy(e, o) {
	//gets mouse position relative to object o
	if (c) {
		var bo = getpos(o);
		var clientX = e.targetTouches && e.targetTouches.length ? e.targetTouches[0].clientX : e.clientX;
		var clientY = e.targetTouches && e.targetTouches.length ? e.targetTouches[0].clientY : e.clientY;
		var x = clientX - bo.x + wsp.scrollLeft;	//correct for canvas position, workspace scroll offset
		var y = clientY - bo.y + wsp.scrollTop;									
		x += document.documentElement.scrollLeft;	//correct for window scroll offset
		y += document.documentElement.scrollTop;									
		return { x: x-.5, y: y-.5 }; //-.5 prevents antialiasing of stroke lines
	}
}

function getpos(o) {
//gets position of object o
	var bo, x, y, b; x = y = 0;
	if(document.getBoxObjectFor) {	//moz
		bo = document.getBoxObjectFor(o);
		x = bo.x; y = bo.y;
	} else if (o.getBoundingClientRect) { //ie (??)
		bo = o.getBoundingClientRect();
		x = bo.left; y = bo.top;
	} else { //opera, safari etc
		while(o && o.nodeName != 'BODY') {
			x += o.offsetLeft;
			y += o.offsetTop;
			b = parseInt(document.defaultView.getComputedStyle(o,null).getPropertyValue('border-width'));
			if(b > 0) { x += b; y +=b; }
			o = o.offsetParent;
		}
	}
	return { x:x, y:y }
}

function blurImage(realCanvas, simulateCanvas, layerId, startX, startY, endX, endY) {
  var x = startX < endX ? startX : endX;
  var y = startY < endY ? startY : endY;
  var width = Math.abs(endX - startX - 1);
  var height = Math.abs(endY - startY - 1);
  //simulateCanvas.width = $(layerId).clientWidth + 10; //canvas.style.width
  //simulateCanvas.height = $(layerId).clientHeight + 10;
  var ctx = simulateCanvas.getContext('2d');
  try {
    ctx.drawImage(realCanvas, x, y, width, height, 0, 0, width, height);
  } catch(error) {
    console.log(error + ' width : height' + width + ' : ' + height) ;
  }
  var imageData = ctx.getImageData(0, 0, width, height);
  imageData = boxBlur(imageData, width, height, 10);
  ctx.putImageData(imageData, 0, 0);
}

function boxBlur(image, width, height, count) {
  var j;
  var pix = image.data;
  var inner = 0;
  var outer = 0;
  var step = 0;
  var rowOrColumn;
  var nextPosition;
  var nowPosition;
  for(rowOrColumn = 0; rowOrColumn < 2; rowOrColumn++) {
    if (rowOrColumn) {
      // column blurring
      outer = width;
      inner = height;
      step = width * 4;
    } else {
      // Row blurring
      outer = height;
      inner = width;
      step = 4;
    }
    for (var i = 0; i < outer; i++) {
      // Calculate for r g b a
      nextPosition = (rowOrColumn == 0 ? (i * width * 4) : (4 * i));
      for (var k = 0; k < 4; k++) {
        nowPosition = nextPosition + k;
        var pixSum = 0;
          for(var m = 0; m < count; m++) {
            pixSum += pix[nowPosition + step * m];
          }
          pix[nowPosition] = pix[nowPosition + step] =
              pix[nowPosition + step * 2] = Math.floor(pixSum/count);
          for (j = 3; j < inner-2; j++) {
            pixSum = Math.max(0, pixSum - pix[nowPosition + (j - 2) * step]
                + pix[nowPosition + (j + 2) * step]);
            pix[nowPosition + j * step] = Math.floor(pixSum/count);
          }
          pix[nowPosition + j * step] = pix[nowPosition + (j + 1) * step] =
              Math.floor(pixSum / count);
      }
    }
  }
  return image;
}

var tool = {

	_shapes: function() {

		this.down = this._down = function() {
			activateTempCanvas();
			this.start = { x:m.x, y:m.y } 
			this.status = 1;
			c.beginPath();
		}
		this._move = function() {
			ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
		}
		this._up = function() {
			canvastemp.style.display='none';
			this.status = 0;
		}

	},

	_brushes: function() {

		this.down = this._down = function() {
			this.last = null;
			this.cp = null;
			this.lastcp = null;
			this.disconnected = null;
			c.beginPath();

			this.sstart = this.last = { x:m.x, y:m.y } //extra s in sstart to not affect status bar display
			this.status = 1;
		}
		this.move = function(e) { 

			if(this.disconnected) {	//re-entering canvas: dont draw a line
				this.disconnected = null;
				this.last = { x:m.x, y:m.y }
			} else {				//draw connecting line
				this.draw();
			}
			c.moveTo(m.x, m.y);

		}
		this.up = function() {
			if(this.sstart && this.sstart.x == m.x && this.sstart.y == m.y) {
				drawDot(m.x, m.y, c.lineWidth, c.strokeStyle);
			}
			this.sstart = null;
			this.status = 0;
			
			undoSave();
		}
		this.draw = function() {
			if(prefs.pretty) { 
				//calculate control point
				this.cp = { x:m.x, y:m.y } //default: no bezier	
				var deltax = Math.abs(m.x-this.last.x);
				var deltay = Math.abs(m.y-this.last.y);
				if(this.last && (deltax+deltay > 10)) { //long line

					//had no control point last time: use last vertex
					var lx = (this.lastcp) ? this.lastcp.x : this.last.x;	//should be last2x?
					var ly = (this.lastcp) ? this.lastcp.y : this.last.y;
					var delta2x = this.last.x-lx;	var delta2y = this.last.y-ly;
 					this.cp = { x:lx+delta2x*1.4, y:ly+delta2y*1.4 }

				}
				this.lastcp = { x:this.cp.x, y:this.cp.y }

				c.bezierCurveTo(this.cp.x, this.cp.y, m.x, m.y, m.x, m.y);  //make pretty curve, first two params =control pt
				c.stroke();	
				c.beginPath();
				if(prefs.controlpoints) { 
					if(!(this.cp.x==m.x && this.cp.y==m.y)) { drawDot(this.cp.x, this.cp.y, 3, 'blue'); }
					drawDot(this.last.x, this.last.y, 3, 'red');
				}

			} else { //unpretty
				c.lineTo(m.x, m.y);
				c.stroke();	
				c.beginPath();
				if(prefs.controlpoints) { 
					drawDot(m.x, m.y, 3, 'red');
				}
			}
				
			this.last = { x:m.x, y:m.y }
		}

	},

	pencil: function() {
		this.name = 'pencil';
		this.status = 0;
		this.inherit = tool._brushes; this.inherit();
		c.lineCap = 'round';
		c.strokeStyle = c.fillStyle = drawingColor;
	},

	brush: function() {
		this.name = 'brush';
		this.status = 0;
		this.inherit = tool._brushes; this.inherit();
	},

	text: function() {
		this.name = 'text';
		this.status = 0;
		this.inherit = tool._shapes; this.inherit();

		c.strokeStyle = c.fillStyle = drawingColor;

		this.down = function(e) {
			console.log("down", e);
			
			this._down();
			if ($("#text").is(":visible")) {
				$("#text").blur();
				document.getElementById("canvas").className = "text";
			} else {
				var width = Math.min(400, $("canvas").width() - m.x + 6);
				var height = Math.min(25, $("canvas").height() - m.y + 13);
				$("#text")
					.TextAreaExpander(height)
					.css(
						{
							left: $("canvas").offset().left + m.x - 2,
							top: m.y - 10,
							width: 200,
							height: height
						}
					)
					.val("")
					.show()
					.focus()
				;
				//document.getElementById("canvas").className = "auto";
			}
		}
		this.up = function(e) {
			this._up();
		}
	},

	line: function() {

		this.name = 'line';
		this.status = 0;
		this.inherit = tool._shapes; this.inherit();

		c.lineCap = 'round';
		c.strokeStyle = c.fillStyle = drawingColor;

		this.move = function(e) {
			this._move();
			drawLine(this.start.x, this.start.y, m.x, m.y, e.shiftKey, ctemp);
		}
		this.up = function(e) {
			this._up();
			drawLine(this.start.x, this.start.y, m.x, m.y, e.shiftKey, c);
			undoSave();
		}

	},

	highlight: function() {
		this.name = 'highlight';
		this.status = 0;
		this.inherit = tool._brushes; this.inherit();

		c.globalAlpha = 0.05;
		c.strokeStyle = "yellow";

		c.lineCap = 'square';
		c.lineWidth = 30;
		c.lastStrokeStyle = c.strokeStyle;
		c.shadowBlur = 0;
		c.shadowColor = null;
	},

	eraser: function() {
		this.name = 'eraser';
		this.status = 0;
		this.inherit = tool._brushes; this.inherit();
		
		c.lineWidth = 1;

		var $canvas = $(canvas);
		var canvasOffset = $canvas.offset();
		var offsetX = canvasOffset.left;
		var offsetY = canvasOffset.top;
		
		this.down = function(e) {
			this._down();
			this.move(e);
		}
		
		this.move = function(e) {
		    mouseX = parseInt(e.clientX - offsetX);
		    mouseY = parseInt(e.clientY - offsetY);
		    
		    boxBlurCanvasRGBA("canvas", mouseX-12, mouseY-12, 24, 24, 2, 0);
		}

	},

	arrow: function() {

		this.name = 'arrow';
		this.status = 0;
		this.inherit = tool._shapes; this.inherit();

		c.lineCap = 'round';
		//c.lineWidth = 3;
		c.strokeStyle = c.fillStyle = drawingColor;

		this.move = function(e) {
			this._move();
			drawArrow(this.start.x, this.start.y, m.x, m.y, e.shiftKey, ctemp);
		}
		this.up = function(e) {
			this._up();
			drawArrow(this.start.x, this.start.y, m.x, m.y, e.shiftKey, c);
			undoSave();
		}

	},


	rectangle: function() {

		this.name = 'rectangle';
		this.status = 0;
		this.inherit = tool._shapes; this.inherit();

		//c.lineWidth = 3;
		c.strokeStyle = c.fillStyle = drawingColor;

		this.move = function(e) {
			this._move();
			drawRectangle(this.start.x, this.start.y, m.x, m.y, e.shiftKey, ctemp);
		}
		this.up = function(e) {
			this._up();
			drawRectangle(this.start.x, this.start.y, m.x, m.y, e.shiftKey, c);
			undoSave();
		}

	},


	ellipse: function() {

		this.name = 'ellipse';
		this.status = 0;
		this.inherit = tool._shapes; this.inherit();

		//c.lineWidth = 3;
		c.strokeStyle = c.fillStyle = drawingColor;

		this.down = function(e) {
			this._down();
			this.lastLineWidth = c.lineWidth;
			if(c.strokeFill == 3) { c.lineWidth+=1.1; ctemp.lineWidth+=1.1; } //hm
		}
		this.move = function(e) {
			this._move();
			drawEllipse(this.start.x, this.start.y, m.x, m.y, e.shiftKey, ctemp);
		}
		this.up = function(e) {
			this._up();
			drawEllipse(this.start.x, this.start.y, m.x, m.y, e.shiftKey, c);
			if (c.strokeFill == 3) {
				c.lineWidth = this.lastLineWidth;
				ctemp.lineWidth = this.lastLineWidth;
			}
			undoSave();
		}

	},


	rounded: function() {

		this.name = 'rounded';
		this.status = 0;
		this.inherit = tool._shapes; this.inherit();
		
		this.move = function(e) {
			this._move();
			drawRounded(this.start.x, this.start.y, m.x, m.y, e.shiftKey, ctemp);
		}
		this.up = function(e) {
			this._up();
			drawRounded(this.start.x, this.start.y, m.x, m.y, e.shiftKey, c);
		}

	},


	curve: function() {

		this.name = 'curve';
		this.status = 0;

		c.lineCap = 'round';
		c.lineWidth = 1;

		this.down = function() {
			if(this.status==0) { //starting
				activateTempCanvas();
				this.start = { x:m.x, y:m.y } 
				this.end = null;
				this.bezier1 = null;
				this.status = 5;
				c.beginPath();
			} else if(this.status==4 || this.status==2) { //continuing
				this.status--;
			}
		}
		this.move = function(e) { 
			if(this.status==5) {

				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
				drawLine(this.start.x, this.start.y, m.x, m.y, e.shiftKey, ctemp);
				ctemp.stroke();

			} else if(this.status == 3 || this.status == 1) {

				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);

				ctemp.moveTo(this.start.x, this.start.y);
				var b1x = (this.bezier1) ? this.bezier1.x : m.x;
				var b1y = (this.bezier1) ? this.bezier1.y : m.y;
				var b2x = (this.bezier1) ? m.x : this.end.x;
				var b2y = (this.bezier1) ? m.y : this.end.y;

				ctemp.bezierCurveTo(b1x, b1y, b2x, b2y, this.end.x, this.end.y);
				ctemp.stroke();
			}
		}
		this.up = function() {
			if(this.status==5) { //setting endpoint     // && source.id != 'body'
				this.end = { x:m.x, y:m.y }
				this.status = 4;
			} else if(this.status==3) { //setting bezier1  && source.id != 'body'
				this.bezier1 = { x:m.x, y:m.y }
				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
				ctemp.moveTo(this.start.x, this.start.y);
				ctemp.bezierCurveTo(m.x, m.y, this.end.x, this.end.y, this.end.x, this.end.y);
				ctemp.stroke();
				this.status = 2;
			} else if(this.status==1) { //setting bezier2  && source.id != 'body'
				canvastemp.style.display='none';
				c.moveTo(this.start.x, this.start.y);
				c.bezierCurveTo(this.bezier1.x, this.bezier1.y,  m.x, m.y, this.end.x, this.end.y);
				c.stroke();
				this.status = 0;
			}
		}
	
	},

	
	polygon: function() {

		this.name = 'polygon';
		this.status = 0;
		this.points = new Array();

		this.down = function() {
			if(this.status==0) { //starting poly
				activateTempCanvas();
				this.start = { x:m.x, y:m.y } 
				this.last = null;
				this.status = 3;
				this.points = new Array();
				c.beginPath();
			} else if(this.status==1) { //adding points
				this.status = 2;
			}	
		}
		this.move = function(e) { 
			if(this.status == 3) { //first polyline
				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
				drawLine(this.start.x, this.start.y, m.x, m.y, e.shiftKey, ctemp);
			} else if(this.status == 2) { // next polyline
				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
				drawLine(this.last.x, this.last.y, m.x, m.y, e.shiftKey, ctemp);
			}
		}
		this.up = function(e) {
			if(Math.abs(m.x-this.start.x) < 4 && Math.abs(m.y-this.start.y) < 4) { //closing
				this.close();
			} else {
				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
				var fromx = (this.status==2) ? this.last.x : this.start.x;
				var fromy = (this.status==2) ? this.last.y : this.start.y;
				var end = drawLine(fromx, fromy, m.x, m.y, e.shiftKey, c); //TODO cant drawline on c yet...3rd canvas??
				this.last = { x:m.x, y:m.y };
				this.points[this.points.length] = { x:m.x, y:m.y };
				this.status = 1;
			}
			trg.stroke();

			undoSave();
		}
		this.close = function() {
			if(this.last.x) { // not just started			
				c.beginPath();
				c.moveTo(this.start.x, this.start.y);
				for(var i=0; i<this.points.length; i++) {
					c.lineTo(this.points[i].x, this.points[i].y);
				}
				c.lineTo(this.last.x, this.last.y);
				c.lineTo(this.start.x, this.start.y);
				if(c.strokeFill == 2 || c.strokeFill == 3) { c.fill(); }
				if(c.strokeFill == 1 || c.strokeFill == 3) { c.stroke(); }

				c.fill();
			}
			canvastemp.style.display='none';
			this.status = 0;
		}

	},


	airbrush: function() {
	
		this.name = 'airbrush';
		this.status = 0;

		c.lineCap = 'square';

		this.down = function() {
			this.drawing = setInterval(c.tool.draw, 50);
			this.last = { x:m.x, y:m.y }
			this.lineCap = 'square';
			this.status = 1;
		}
		this.move = function(e) { 
			this.last = { x:m.x, y:m.y }
		}
		this.up = function(e) {
			clearInterval(this.drawing);
			this.status = 0;
			undoSave();
		}
		
		this.draw = function() {
			c.save();
			c.beginPath();
			c.arc(this.last.x, this.last.y, c.lineWidth*4, 0, Math.PI*2, true);
			c.clip();
			for(var i=c.lineWidth*15; i>0; i--) {
				var rndx = c.tool.last.x + Math.round(Math.random()*(c.lineWidth*8)-(c.lineWidth*4));
				var rndy = c.tool.last.y + Math.round(Math.random()*(c.lineWidth*8)-(c.lineWidth*4));
				drawDot(rndx, rndy, 1, c.strokeStyle);
			}
			c.restore();
		}


	},

	floodfill: function() {
	  
		this.name = 'floodfill';
		this.status = 0;
		
		this.down = function(e) {
	        var x = Math.round(m.x);
	        var y = Math.round(m.y);
	
	        var oldColor = getPixel(x, y);
	        if(!oldColor) { alert('Sorry, your browser doesn\'t support flood fill.'); return false; } 
	        if(oldColor == c.strokeStyle) { return; }
	        
	        var stack = [{x:x, y:y}];
	                       
	        //var n = 0;
	        while(popped = stack.pop()) {
	            //n++;
	            var x = popped.x;   
	            var y1 = popped.y;
	            while(getPixel(x, y1) == oldColor && y1 >= 0) { y1--; }
	            y1++;
	            var spanLeft = false;
	            var spanRight = false;
	            while(getPixel(x, y1) == oldColor && y1 < canvas.height) {
	                if(window.opera) { 
	                  co.setPixel(x, y1, c.strokeStyle);
	                } else {
	                  //c.beginPath();
	                  c.fillStyle = c.strokeStyle;
	                  c.fillRect(x, y1, 1, 1);
	                  //drawDot(x, y1, 1, c.strokeStyle, c);
	                  //document.getElementById('info').innerHTML += '<br />'+x+'/'+y1;
	                }
	                if(!spanLeft && x > 0 && getPixel(x-1, y1) == oldColor) {
	                  //break;
	                   stack.push({x:x-1, y:y1});        
	                    spanLeft = true;
	                } else if(spanLeft && x > 0 && getPixel(x-1, y1) != oldColor) {
	                    spanRight = false;
	                } else if(spanRight && x <= 0) { spanRight = false; }
	                if(!spanRight && x < canvas.width-1 && getPixel(x+1, y1) == oldColor) {
	                  //break;
	                  stack.push({x:x+1, y:y1});
	                    spanRight = true;
	                } else if(spanRight && x < canvas.width-1 && getPixel(x+1, y1) != oldColor) {
	                    spanRight = false;
	                } else if(spanRight && x >= canvas.width) { spanRight = false; }
	                y1++;                   
	            }
	        }        
	        
	        
	        if(window.opera) {
	          co.lockCanvasUpdates(false);
	          co.updateCanvas();
	        }
	        //document.getElementById('info').innerHTML = check;
        
		}
		
    this.move = function() { }
		this.up = function() { }
	  
	},

	select: function() {

		this.name = 'select';
		this.status = 0;

		c.lastTool = c.tool.name;
		c.lineWidth = 1;
		c.lastStrokeStyle = c.strokeStyle;
		c.strokeStyle = c.createPattern(dashed, 'repeat');
		//c.strokeFill = 1;
		c.beginPath();

		this.down = function(e) { 
			console.log("selectdown", this.status);
			if (this.status==0) { //starting select
				c.strokeStyle = c.createPattern(dashed, 'repeat');
				activateTempCanvas();
				this.start = { x:m.x, y:m.y } 
				this.status = 4;
			} else if (this.status==2 || this.status==3) { //moving selection
				if (intersects(m, this.start, this.dimension)) {
					this.offset = { x:m.x-this.start.x, y:m.y-this.start.y } 
					if (this.status == 3 && !e.ctrlKey && !e.shiftKey) { //when first moving (and not in stamp mode), clear original pos and paint on tempcanvas
						var pos = { x:m.x-this.offset.x, y:m.y-this.offset.y }						
						drawRectangle(pos.x-1, pos.y-1, pos.x+this.dimension.x, pos.y+this.dimension.y, null, ctemp);
						
						ctemp.scale(1 / devicePixelRatio, 1 / devicePixelRatio);
						ctemp.drawImage(canvassel, Math.floor(pos.x) * devicePixelRatio, Math.floor(pos.y) * devicePixelRatio);
						//ctemp.scale(devicePixelRatio, devicePixelRatio);
						
						c.fillStyle = FILL_STYLE;
						c.fillRect(this.start.x-.5, this.start.y-.5, this.dimension.x, this.dimension.y);
					}
					this.status = 1;
				} else {  //starting new selection
					if (this.status < 3) { //actually draw last moved selection to canvas TODO also do this when switching tools
						console.log("last drawn")
						c.scale(1 / devicePixelRatio, 1 / devicePixelRatio);
						c.drawImage(canvassel, Math.floor(this.start.x * devicePixelRatio), Math.floor(this.start.y * devicePixelRatio));
						c.scale(devicePixelRatio, devicePixelRatio);
						
						ctemp.scale(devicePixelRatio, devicePixelRatio);
					}
					activateTempCanvas();
					this.start = { x:m.x, y:m.y } 
					this.status = 4;
				}
			}
		}
		this.move = function(e) {
			console.log("selectmove", this.status);
			if (this.status==4) { //selecting
				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
				ctemp.strokeStyle = c.createPattern(dashed, 'repeat');
				var constrained = { x:constrain(m.x, 0, canvas.width), y:constrain(m.y, 0, canvas.height-5) }
				drawRectangle(this.start.x-1, this.start.y-1, constrained.x, constrained.y, null, ctemp);

			} else if (this.status==1) { //moving selection
				ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
				var pos = { x:m.x-this.offset.x, y:m.y-this.offset.y }
				drawRectangle((pos.x* devicePixelRatio)-1 , (pos.y* devicePixelRatio)-1, (pos.x+this.dimension.x) * devicePixelRatio, (pos.y+this.dimension.y) * devicePixelRatio, null, ctemp);
				
				//ctemp.scale(1 / devicePixelRatio, 1 / devicePixelRatio);
				ctemp.drawImage(canvassel, Math.floor(pos.x) * devicePixelRatio, Math.floor(pos.y) * devicePixelRatio);
				//ctemp.scale(devicePixelRatio, devicePixelRatio);
				
				if (e.shiftKey) { //dupli mode
					c.drawImage(canvassel, Math.floor(pos.x), Math.floor(pos.y));
				}
			} else if (this.start) {
				if (c.tool.status == 1 || (c.tool.dimension && intersects(m, c.tool.start, c.tool.dimension))) {
					canvastemp.style.cursor = 'move';
				} else {
					canvastemp.style.cursor = '';		
				}
			}

		}
		this.up = function(e) {
			console.log("selectup", this.status);
			if (this.status == 4) { //finished selecting

				this.status = 3;
				this.dimension = { x:constrain(m.x, 0, canvas.width)-this.start.x,
								   y:constrain(m.y, 0, canvas.height)-this.start.y }
				if (this.dimension.x == 0 && this.dimension.y == 0) { //nothing selected, abort
					this.status = 0;
					canvastemp.style.display='none';
					csel.clearRect(0, 0, canvassel.width, canvassel.height);
				} else { //save on selection canvas
					csel.clearRect(0, 0, canvassel.width, canvassel.height);
					if (this.dimension.x < 0) { this.start.x = this.start.x + this.dimension.x; this.dimension.x *= -1; } //correct for selections not drawn from top left
					if (this.dimension.y < 0) { this.start.y = this.start.y + this.dimension.y; this.dimension.y *= -1; }
					//todo check for >max
					
					console.log("selectupthis", this)
					csel.scale(devicePixelRatio, devicePixelRatio);
					csel.drawImage(canvas, Math.floor(this.start.x) * devicePixelRatio, Math.floor(this.start.y) * devicePixelRatio, this.dimension.x * devicePixelRatio, this.dimension.y * devicePixelRatio, 0, 0, this.dimension.x, this.dimension.y);
					csel.scale(1 / devicePixelRatio, 1 / devicePixelRatio);
					
					csel.dimension = this.dimension;
					
					if (clickedTool == "crop") {
						canvas.width = this.dimension.x * devicePixelRatio;
						canvas.height = this.dimension.y * devicePixelRatio;
						
						canvastemp.width = canvas.width;
						canvastemp.height = canvas.height;
						
						canvas.style.width = canvastemp.style.width = (canvas.width / devicePixelRatio) + 'px';
						canvas.style.height = canvastemp.style.height = (canvas.height / devicePixelRatio) + 'px';
						
						c.drawImage(canvassel, 0, 0);

						c.scale(devicePixelRatio, devicePixelRatio);
						ctemp.scale(devicePixelRatio, devicePixelRatio);

						canvasLeft = $("canvas").offset().left;
						$("#canvastemp").css("left", canvasLeft );

						this.status = 0;
						canvastemp.style.display='none';
						csel.clearRect(0, 0, canvassel.width, canvassel.height);
					}
				}
			} else if (this.status == 1) { //finished moving selection
				this.status = 2;
				this.start = { x:m.x-this.offset.x, y:m.y-this.offset.y }
				if (e.ctrlKey) { //stamp mode
					c.drawImage(canvassel, Math.floor(this.start.x), Math.floor(this.start.y));
				}
			}

			undoSave();
		}

		this.del = function() { 
			c.fillStyle = FILL_STYLE;
			c.fillRect(this.start.x-.5, this.start.y-.5, this.dimension.x, this.dimension.y);
			activateTempCanvas(); 
			canvastemp.style.display = 'none';
			this.status = 0;
			undoSave();
		}
		this.all = function() { 
			csel.clearRect(0, 0, canvassel.width, canvassel.height);
			csel.drawImage(canvas, 0, 0);
			activateTempCanvas();
			this.start = { x:0.5, y:0.5 }
			this.dimension = { x:canvas.width, y:canvas.height }		
			ctemp.strokeRect(0.5, 0.5, canvas.width-1, canvas.height-1);
			this.status = 3;
		}
		this.copy = function() {
			csel.drawImage(canvas, Math.floor(this.start.x), Math.floor(this.start.y), this.dimension.x, this.dimension.y, 0, 0, this.dimension.x, this.dimension.y);
			csel.dimension = this.dimension;
		}
		this.paste = function() {
			activateTempCanvas();
			ctemp.drawImage(canvassel, 0, 0);
			this.status = 3;
			this.start = { x:.5, y:.5 }
			this.dimension = csel.dimension;
			ctemp.strokeRect(this.start.x-.5, this.start.y-.5, this.dimension.x+.5, this.dimension.y+.5);

		}

	}

}


function getPixel(x, y) {
 
  if(c.getImageData) {
      return false;
  } else if (window.opera) {
    if(!co) { co = canvas.getContext('opera-2dgame');	}
    col = co.getPixel(x, y);
    //check += '<br />'+x+'/'+y+': '+col;
    return col;
  } else {
    return false; 
  }
  
}  


function c_down(e) {
	console.log("c_down", e);
	
	// when touchevent button is undefined
	if (e.button == undefined || e.button == 0) {
		//handles mousedown on the canvas depending on tool selected
		var source = e.currentTarget;
		m = getxy(e, canvas);

		if(c.tool.name != 'select' && c.tool.name != 'eraser') { //no color switching for these
			if(e.ctrlKey) {							 //ctrl: switch tert & stroke
				var temp = c.tertStyle;
				c.tertStyle = c.strokeStyle;
				c.strokeStyle = temp;
			}
			if(e.button == 2 && c.tool.name != 'eraser') { //right: switch stroke & fill
				var temp = c.strokeStyle;
				c.strokeStyle = c.fillStyle;
				c.fillStyle = temp;
			}
		}

		c.tool.down(e);
		c.moveTo(m.x, m.y); //?
	}
	return false;
}


function c_up(e) {
	console.log("c_up");

	if (e.button == undefined || e.button == 0) {
		// handles mouseup on the canvas depending on tool selected
		$("#refresh, #undo").css("visibility", "visible");
		
		e.stopPropagation();
		if(c.resizing) { bodyUp(e); } //but not if dragging

		c.tool.up(e);
		if(c.tool.name != 'select' && c.tool.name != 'eraser') { //no color switching for these
			if(e.button == 2 && c.tool.name != 'eraser') { //right: switch stroke & fill back
				var temp = c.fillStyle;
				c.fillStyle = c.strokeStyle;
				c.strokeStyle = temp;
			}
			if(e.ctrlKey) { 
				var temp = c.strokeStyle;
				c.strokeStyle = c.tertStyle;
				c.tertStyle = temp;
			}
		}
		//c.strokeStyle = c.fillStyle;		
	}
	return false;
}

function c_move(e) {
	m = getxy(e, canvas);
	e.stopPropagation();
	if (c.resizing) { bodyMove(e); } //don't stop propagation if dragging

	if (c.tool.status > 0 && c.tool.move) {
		c.tool.move(e);
	}

	return false;
}

function c_out(e) {
	if(c && (c.tool.name=='pencil' || c.tool.name=='brush') && c.tool.status==1) { 
		c.tool.disconnected = 1;
		m = getxy(e, canvas);
		c.tool.draw();
	}

}

function activateTempCanvas() {
	//resets and shows overlay canvas
	if(m) { ctemp.moveTo(m.x, m.y); }							//copy context from main
	ctemp.lineCap = c.lineCap;								
	ctemp.lineWidth = c.lineWidth;
	ctemp.strokeStyle = c.strokeStyle;
	ctemp.fillStyle = c.fillStyle;
	ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);	//clear
	canvastemp.style.display='block';							//show
}

function canvasResize(e) {
	c.resizing = true;
	document.body.style.cursor = 'nw-resize';
	canvastemp.lastCursor = canvastemp.style.cursor;
	canvastemp.style.cursor = 'nw-resize';
	activateTempCanvas();
	var dotted = new Image(); dotted.src = 'icons/dotted.gif';
	ctemp.strokeStyle = ctemp.createPattern(dotted, 'repeat');
}


function clipResize(w, h) { 
	//resizes all the canvases by clipping/extending, moves resize handle
	canvas.width = canvastemp.width = canvassel.width = w;
	canvas.height = canvastemp.height = canvassel.height = h;
	canvas.style.width = canvastemp.style.width = w+'px';
	canvas.style.height = canvastemp.style.height = h+'px';
	var cresizer = document.getElementById('canvasresize');
	if (cresizer) {
		cresizer.style.left = w+cresizer.offsetWidth+'px'; cresizer.style.top = h+cresizer.offsetHeight+'px';
	}
	c.fillRect(0, 0, canvas.width, canvas.height); //so that if expanding it's filled with bg col
	undoSave();
}


function bodyMove(e) {
	//lets the user move outside the canvas while drawing shapes and lines

	if(c.tool.status > 0) { c_move(e); }	

	if(c.resizing) {	
		m = getxy(e, document.body);
		var win = wsp.parentNode.parentNode.parentNode;
		ctemp.clearRect(0, 0, canvastemp.width, canvastemp.height);
		ctemp.strokeRect(0, 0, m.x, m.y); //dotted line
	}

}


function bodyUp(e) {
	//stops drawing even if mouseup happened outside canvas
	//closes menus if clicking somewhere else
	if (c.resizing) {
		c.resizing = false; document.body.style.cursor = 'auto'; canvastemp.style.cursor = canvastemp.lastCursor;
		m = getxy(e, wsp);
		clipResize(m.x-3, m.y-3);
	}

	if(c.tool.name == 'select') { //cancel selection or finalize selection move
	    sel_cancel();
	}

	if(c && c.tool.name != 'polygon' && c.tool.status > 0) {
		c_up(e);
	}
	/*
	if(document.getElementById('menubar').className=='open') {
		document.getElementById('menubar').className='';
		e.stopPropagation();
	}
	*/
}

function drawDot(x, y, size, col, trg) {
	x = Math.floor(x)+1; //prevent antialiasing of 1px dots
	y = Math.floor(y)+1;

	if(x>0 && y>0) {

		if(!trg) { trg = c; }
		if(col || size) { var lastcol = trg.fillStyle; var lastsize = trg.lineWidth; }
		if(col)  { trg.fillStyle = col;  }
		if(size) { trg.lineWidth = size; }	
		if(trg.lineCap == 'round') {
			trg.arc(x, y, trg.lineWidth/2, 0, (Math.PI/180)*360, false);
			trg.fill();
		} else {
			var dotoffset = (trg.lineWidth > 1) ? trg.lineWidth/2 : trg.lineWidth;
			trg.fillRect((x-dotoffset), (y-dotoffset), trg.lineWidth, trg.lineWidth);
		}
		if(col || size) { trg.fillStyle = lastcol; trg.lineWidth = lastsize; }

	}
}

function drawLine(x1, y1, x2, y2, mod, trg) { 

	if(trg.lineWidth % 2 == 0) { x1 = Math.floor(x1); y1 = Math.floor(y1); x2 = Math.floor(x2); y2 = Math.floor(y2); } //no antialiasing

	trg.beginPath();
	trg.moveTo(x1, y1);
	if(mod) {
		var dx = Math.abs(x2-x1);
		var dy = Math.abs(y2-y1);	
		var dd = Math.abs(dx-dy);
		if(dx > 0 && dy > 0 && dx != dy) {
			if(dd < dx && dd < dy) { //diagonal
				if(dx < dy) {
					y2 = y1+(((y2-y1)/dy)*dx);
				} else {
					x2 = x1+(((x2-x1)/dx)*dy);
				}
			} else if(dx < dy) {
				x2 = x1;
			} else if(dy < dx) {
				y2 = y1;
			}
		}
	}
	trg.lineTo(x2, y2);
	trg.stroke();
	trg.beginPath();
	return { x:x2, y:y2 }
}

function drawArrow(x1, y1, x2, y2, mod, trg) { 

	if(trg.lineWidth % 2 == 0) { x1 = Math.floor(x1); y1 = Math.floor(y1); x2 = Math.floor(x2); y2 = Math.floor(y2); } //no antialiasing

	trg.beginPath();
	trg.moveTo(x1, y1);

	var dx = Math.abs(x2-x1);
	var dy = Math.abs(y2-y1);	
	var dd = Math.abs(dx-dy);
	
	if (mod) { // not free hand angles
		if(dx > 0 && dy > 0 && dx != dy) {
			if(dd < dx && dd < dy) { //diagonal
				if(dx < dy) {
					y2 = y1+(((y2-y1)/dy)*dx);
				} else {
					x2 = x1+(((x2-x1)/dx)*dy);
				}
			} else if(dx < dy) {
				x2 = x1;
			} else if(dy < dx) {
				y2 = y1;
			}
		}
	}

	var ArrowHeadLength = 16;
	
	var LineAngle = Math.atan((y2-y1)/(x2-x1));
	var EndAngle1 = LineAngle + 35 * Math.PI/180;
	var EndAngle2 = LineAngle - 35 * Math.PI/180;

	
	var angle = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI;
	
	//console.log(angle);
	//console.log(x2-x1, y2-y1);
	//console.log(LineAngle, EndAngle1, EndAngle2)
	
	// jason correced the arrow just a bit
	if (-90 <= angle && angle <= 0) {
		trg.lineTo(x2, y2+1);
	} else if (90 < angle && angle <= 180) {
		trg.lineTo(x2, y2-1);
	} else {
		trg.lineTo(x2, y2);
	}
	
	dir=1
	if (x2<x1) {
		dir=-1;
	}
	var x3 = x2 - ArrowHeadLength * Math.cos(EndAngle1) * dir;
	var y3 = y2 - ArrowHeadLength * Math.sin(EndAngle1) * dir;
	
	var x4 = x2 - ArrowHeadLength * Math.cos(EndAngle2) * dir;
	var y4 = y2 - ArrowHeadLength * Math.sin(EndAngle2) * dir;



	trg.moveTo(x2+dir, y2+dir);
	trg.lineTo(x3, y3);
	trg.moveTo(x2+dir, y2+dir);
	trg.lineTo(x4, y4);

	trg.stroke();
	trg.beginPath();
	return { x:x2, y:y2 }
}

function drawEllipse(x1, y1, x2, y2, mod, trg) {
	//bounding box. this maybe isn't the best idea?
	 
	var dx = Math.abs(x2-x1);
	var dy = Math.abs(y2-y1);
	
	if(mod && !(dx==dy)) { 	//shift held down: constrain
		if(dx < dy) {
			x2 = x1+(((x2-x1)/dx)*dy);
		} else {
  		y2 = y1+(((y2-y1)/dy)*dx);
		} 
	}

  var KAPPA = 4 * ((Math.sqrt(2) -1) / 3);
	var rx = (x2-x1)/2;
	var ry = (y2-y1)/2;	
  var cx = x1+rx;
  var cy = y1+ry;

	trg.beginPath();
  trg.moveTo(cx, cy - ry);
  trg.bezierCurveTo(cx + (KAPPA * rx), cy - ry,  cx + rx, cy - (KAPPA * ry), cx + rx, cy);  
  trg.bezierCurveTo(cx + rx, cy + (KAPPA * ry), cx + (KAPPA * rx), cy + ry, cx, cy + ry); 
  trg.bezierCurveTo(cx - (KAPPA * rx), cy + ry, cx - rx, cy + (KAPPA * ry), cx - rx, cy); 
  trg.bezierCurveTo(cx - rx, cy - (KAPPA * ry), cx - (KAPPA * rx), cy - ry, cx, cy - ry); 

	if(c.strokeFill == 1 || c.strokeFill == 3) { trg.stroke(); }
	if(c.strokeFill == 2 || c.strokeFill == 3) { trg.fill();   }
}


function drawRectangle(x1, y1, x2, y2, mod, trg) {
	trg.beginPath();
	var dx = Math.abs(x2-x1);
	var dy = Math.abs(y2-y1);

	if(mod && dx != dy) {	//shift held down: constrain
		if(dx < dy) {
			y2 = y1+(((y2-y1)/dy)*dx);
		} else {
			x2 = x1+(((x2-x1)/dx)*dy);
		}
	}
	
	if(c.strokeFill == 2 || trg.lineWidth % 2 == 0) {    //no antialiasing
		x1 = Math.floor(x1); y1 = Math.floor(y1); x2 = Math.floor(x2); y2 = Math.floor(y2);
	}
	trg.rect(x1, y1, (x2-x1), (y2-y1));
	if(c.strokeFill == 2 || c.strokeFill == 3) { trg.fill(); }
	if(c.strokeFill == 1 || c.strokeFill == 3) { trg.stroke(); }
	trg.beginPath();
}

function drawRounded(x1, y1, x2, y2, mod, trg) {
	var dx = Math.abs(x2-x1);
	var dy = Math.abs(y2-y1);

	if(mod && dx != dy) {	//shift held down: constrain
		if(dx < dy) {
			y2 = y1+(((y2-y1)/dy)*dx);
			dy = dx;
		} else {
			x2 = x1+(((x2-x1)/dx)*dy);
			dx = dy;
		}
	}
	var dmin = (dx < dy) ? dx : dy;
	var cornersize = (dmin/2 >= 15) ? 15 : dmin/2;
	
	var xdir = (x2 > x1) ? cornersize : -1*cornersize;
	var ydir = (y2 > y1) ? cornersize : -1*cornersize;

	drawRounded2(trg, x1, x2, y1, y2, xdir, ydir);
	if(c.strokeFill == 2 || c.strokeFill == 3) { trg.fill(); }
	if(c.strokeFill == 1 || c.strokeFill == 3) { trg.stroke(); }

}

function drawRounded2(trg, x1, x2, y1, y2, xdir, ydir) {
	trg.beginPath();
	trg.moveTo(x1, y1+ydir);
	trg.quadraticCurveTo(x1, y1, x1+xdir, y1);
	trg.lineTo(x2-xdir, y1);
	trg.quadraticCurveTo(x2, y1, x2, y1+ydir);
	trg.lineTo(x2, y2-ydir);
	trg.quadraticCurveTo(x2, y2, x2-xdir, y2);
	trg.lineTo(x1+xdir, y2);
	trg.quadraticCurveTo(x1, y2, x1, y2-ydir);
	trg.closePath();
}

function constrain(n, min, max) {
	if(n > max) return max;
	if(n < min) return min;
	return n;
}

function intersects(m, start, dim) {
//checks if m(x,y) is between start(x,y) and start+dim(x,y)
	if(	m.x >= start.x && m.y >= start.y &&
		m.x <= (start.x+dim.x) && m.y <= (start.y+dim.y)) {
		return true;
	} else {
		return false;
	}
}