/**
* TinyEditor
*
* TinyEditor is a simple JavaScript WYSIWYG editor that is both lightweight (8KB) and standalone.
* It can easily be customized to integrate with any website through CSS and the multitude of parameters.
* It handles most of the basic formatting needs and has some functionality built in to help keep the rendered
* markup as clean as possible. The icons are courtesy of famfamfam
* source:
* - Original version: http://www.scriptiny.com/2010/02/javascript-wysiwyg-editor/
* - Github version: https://github.com/jessegreathouse/TinyEditor
* - Colibrì version: the code from Github has been encapsulated and slightly edited. little information added.
* This plugin NEEDS document.execCommand() to be supported.
*
* @author Michael Leigeber
* @author Jesse Greathouse
* @author Nereo Costacurta
*/

/*
Document.execCommand()
======================
(source: https://developer.mozilla.org/en/docs/Web/API/Document/execCommand)
When an HTML document has been switched to designMode, the document object exposes the execCommand
method which allows one to run commands to manipulate the contents of the editable region. Most
commands affect the document's selection (bold, italics, etc.), while others insert new elements (adding a
link) or affect an entire line (indenting). When using contentEditable, calling execCommand() will
affect the currently active editable element.

Return value
------------
A Boolean that is false if the command is not supported or enabled.

Parameters
----------
aCommandName
	A DOMString specifying the name of the command to execute. See Commands for a list of possible
	commands.
aShowDefaultUI
	A Boolean indicating whether the default user interface should be shown. This is not implemented
	in Mozilla.
aValueArgument
	For commands which require an input argument (such as insertImage, for which this is the URL of
	the image to insert), this is a DOMString providing that information. Specify null if no argument
	is needed.

Commands
--------
backColor
	Changes the document background color. In styleWithCss mode, it affects the background color of
	the containing block instead. This requires a <color> value string to be passed in as a value
	argument. Note that Internet Explorer uses this to set the text background color.
bold
	Toggles bold on/off for the selection or at the insertion point. Internet Explorer uses the
	<strong> tag instead of <b>.
contentReadOnly
	Makes the content document either read-only or editable. This requires a boolean true/false to
	be passed in as a value argument. (Not supported by Internet Explorer.)
copy
	Copies the current selection to the clipboard. Conditions of having this behavior enabled vary
	from one browser to another, and have evolved over time. Check the compatibility table to
	determine if you can use it in your case.
createLink
	Creates an anchor link from the selection, only if there is a selection. This requires the HREF
	URI string to be passed in as a value argument. The URI must contain at least a single character,
	which may be a white space. (Internet Explorer will create a link with a null URI value.)
cut
	Cuts the current selection and copies it to the clipboard. Conditions of having this behavior
	enabled vary from one browser to another, and have evolved over time. Check the compatibility
	table for knowing if you can use it in your case.
decreaseFontSize
	Adds a <small> tag around the selection or at the insertion point. (Not supported by Internet
	Explorer.)
delete
	Deletes the current selection.
enableInlineTableEditing
	Enables or disables the table row and column insertion and deletion controls. (Not supported by
	Internet Explorer.)
enableObjectResizing
	Enables or disables the resize handles on images and other resizable objects. (Not supported by
	Internet Explorer.)
fontName
	Changes the font name for the selection or at the insertion point. This requires a font name
	string ("Arial" for example) to be passed in as a value argument.
fontSize
	Changes the font size for the selection or at the insertion point. This requires an HTML font
	size (1-7) to be passed in as a value argument.
foreColor
	Changes a font color for the selection or at the insertion point. This requires a color value
	string to be passed in as a value argument.
formatBlock
	Adds an HTML block-style tag around the line containing the current selection, replacing the
	block element containing the line if one exists (in Firefox, BLOCKQUOTE is the exception - it
	will wrap any containing block element). Requires a tag-name string to be passed in as a value
	argument. Virtually all block style tags can be used (eg. "H1", "P", "DL", "BLOCKQUOTE").
	(Internet Explorer supports only heading tags H1 - H6, ADDRESS, and PRE, which must also include
	the tag delimiters < >, such as "<H1>".)
forwardDelete
	Deletes the character ahead of the cursor's position.  It is the same as hitting the delete key.
heading
	Adds a heading tag around a selection or insertion point line. Requires the tag-name string to
	be passed in as a value argument (i.e. "H1", "H6"). (Not supported by Internet Explorer and
	Safari.)
hiliteColor
	Changes the background color for the selection or at the insertion point. Requires a color value
	string to be passed in as a value argument. UseCSS must be turned on for this to function. (Not
	supported by Internet Explorer.)
increaseFontSize
	Adds a BIG tag around the selection or at the insertion point. (Not supported by Internet
	Explorer.)
indent
	Indents the line containing the selection or insertion point. In Firefox, if the selection spans
	multiple lines at different levels of indentation, only the least indented lines in the selection
	will be indented.
insertBrOnReturn
	Controls whether the Enter key inserts a br tag or splits the current block element into two.
	(Not supported by Internet Explorer.)
insertHorizontalRule
	Inserts a horizontal rule at the insertion point (deletes selection).
insertHTML
	Inserts an HTML string at the insertion point (deletes selection). Requires a valid HTML string
	to be passed in as a value argument. (Not supported by Internet Explorer.)
insertImage
	Inserts an image at the insertion point (deletes selection). Requires the image SRC URI string
	to be passed in as a value argument. The URI must contain at least a single character, which may
	be a white space. (Internet Explorer will create a link with a null URI value.)
insertOrderedList
	Creates a numbered ordered list for the selection or at the insertion point.
insertUnorderedList
	Creates a bulleted unordered list for the selection or at the insertion point.
insertParagraph
	Inserts a paragraph around the selection or the current line. (Internet Explorer inserts a
	paragraph at the insertion point and deletes the selection.)
insertText
	Inserts the given plain text at the insertion point (deletes selection).
italic
	Toggles italics on/off for the selection or at the insertion point. (Internet Explorer uses the
	EM tag instead of I.)
justifyCenter
	Centers the selection or insertion point.
justifyFull
	Justifies the selection or insertion point.
justifyLeft
	Justifies the selection or insertion point to the left.
justifyRight
	Right-justifies the selection or the insertion point.
outdent
	Outdents the line containing the selection or insertion point.
paste
	Pastes the clipboard contents at the insertion point (replaces current selection). Clipboard
	capability must be enabled in the user.js preference file. See [1].
redo
	Redoes the previous undo command.
removeFormat
	Removes all formatting from the current selection.
selectAll
	Selects all of the content of the editable region.
strikeThrough
	Toggles strikethrough on/off for the selection or at the insertion point.
subscript
	Toggles subscript on/off for the selection or at the insertion point.
superscript
	Toggles superscript on/off for the selection or at the insertion point.
underline
	Toggles underline on/off for the selection or at the insertion point.
undo
	Undoes the last executed command.
unlink
	Removes the anchor tag from a selected anchor link.
useCSS
	Toggles the use of HTML tags or CSS for the generated markup. Requires a boolean true/false as a
	value argument. NOTE: This argument is logically backwards (i.e. use false to use CSS, true to
	use HTML). (Not supported by Internet Explorer.) This has been deprecated; use the styleWithCSS
	command instead.
styleWithCSS
	Replaces the useCSS command; argument works as expected, i.e. true modifies/generates style
	attributes in markup, false generates formatting elements.
*/


TINY = (function(win, doc) {
	if (!('execCommand' in doc)) return {};

	var TINY = {};

	function T$(i) {
		return doc.getElementById(i)
	}

	function newElement(i) {
		return doc.createElement(i)
	}

	function T$$$() {
		return doc.all ? 1 : 0
	}

	TINY.editor = function() {
		var c = {},
			offset = -30;
		//TODO: translate to any language
		
		/*
		* Controls.
		* [<id>, <title>, <action type>, <execCommand>, <params...>]
		* - id = used for buttons background position.
		* - title = title showed on mouse hover
		* - action type: a => action (no dialog required); i => insert (a popup dialog open)
		* - action function: command to execute (see execCommand list)
		* - params: parameter of action function. ()
		*/
		c['cut'] =				 [1,  'Cut',							'a', 'cut', 1, null];			//not reliable!
		c['copy'] =				 [2,  'Copy',							'a', 'copy', 1, null];			//not reliable!
		c['paste'] =			 [3,  'Paste',							'a', 'paste', 1, null];			//not reliable!
		c['bold'] =				 [4,  'Bold',							'a', 'bold'];
		c['italic'] =			 [5,  'Italic',						'a', 'italic'];
		c['underline'] =		 [6,  'Underline',					'a', 'underline'];
		c['strikethrough'] =	 [7,  'Strikethrough',				'a', 'strikethrough'];
		c['subscript'] =		 [8,  'Subscript',					'a', 'subscript'];
		c['superscript'] =	 [9,  'Superscript',					'a', 'superscript'];
		c['orderedlist'] =	 [10, 'Insert Ordered List',		'a', 'insertorderedlist'];
		c['unorderedlist'] =	 [11, 'Insert Unordered List',	'a', 'insertunorderedlist'];
		c['outdent'] =			 [12, 'Outdent',						'a', 'outdent'];					//not reliable!
		c['indent'] =			 [13, 'Indent',						'a', 'indent'];					//not reliable!
		c['leftalign'] =		 [14, 'Left Align',					'a', 'justifyleft'];
		c['centeralign'] =	 [15, 'Center Align',				'a', 'justifycenter'];
		c['rightalign'] =		 [16, 'Right Align',					'a', 'justifyright'];
		c['blockjustify'] =	 [17, 'Block Justify',				'a', 'justifyfull'];
		c['undo'] =				 [18, 'Undo',							'a', 'undo'];						//not reliable!
		c['redo'] =				 [19, 'Redo',							'a', 'redo'];						//not reliable!
		c['image'] =			 [20, 'Insert Image',				'i', 'insertimage', 'Enter Image URL:', 'http://'];
		c['hr'] =				 [21, 'Insert Horizontal Rule',	'a', 'inserthorizontalrule'];
		c['link'] =				 [22, 'Insert Hyperlink',			'i', 'createlink', 'Enter URL:', 'http://'];
		c['unlink'] =			 [23, 'Remove Hyperlink',			'a', 'unlink'];
		c['unformat'] =		 [24, 'Remove Formatting',			'a', 'removeformat'];
		c['print'] =			 [25, 'Print',							'a', 'print'];

		function edit(n, obj) {
			this.n = n;
			win[n] = this;
			this.t = T$(obj.id);
			this.obj = obj;
			this.xhtml = obj.xhtml;
			var p = newElement("div"),
				w = newElement("div"),
				h = newElement("div"),
				l = obj.controls.length,
				i = 0;
			this.i = newElement("iframe");
			this.i.frameBorder = 0;
			this.i.width = obj.width || '500';
			this.i.height = obj.height || '250';
			this.ie = T$$$();
			h.className = obj.rowclass || 'teheader';
			p.className = obj.cssclass || 'te';
			p.style.maxWidth = this.i.width + 'px';
			p.appendChild(h);
			for (i; i < l; i++) {
				var id = obj.controls[i];
				switch (id){
					
					case 'n':
						h = newElement("div");
						h.className = obj.rowclass || 'teheader';
						p.appendChild(h);
					break;
					
					case '|':
						var d = newElement("div");
						d.className = obj.dividerclass || 'tedivider';
						h.appendChild(d);
					break;
					
					case 'font':
						var sel = newElement("select"),
							fonts = obj.fonts || ['Helvetica', 'Arial', 'Verdana', 'Georgia'],
							fl = fonts.length,
							x = 0;
						sel.className = 'tefont';
						sel.onchange = new Function(this.n + '.ddaction(this,"fontname")');
						sel.options[0] = new Option('Font', '');
						for (x; x < fl; x++) {
							var font = fonts[x];
							sel.options[x + 1] = new Option(font, font)
						}
						h.appendChild(sel);
					break;
					
					case 'size':
						var sel = newElement("select"),
							sizes = obj.sizes || [1, 2, 3, 4, 5, 6, 7],
							sl = sizes.length,
							x = 0;
						sel.className = 'tesize';
						sel.onchange = new Function(this.n + '.ddaction(this,"fontsize")');
						for (x; x < sl; x++) {
							var size = sizes[x];
							sel.options[x] = new Option(size, size)
						}
						h.appendChild(sel);
					break;
					
					case 'style':
						var sel = newElement("select"),
							styles = obj.styles || [
								['Style', ''],
								['Paragraph', '<p>'],
								['Header 1', '<h1>'],
								['Header 2', '<h2>'],
								['Header 3', '<h3>'],
								['Header 4', '<h4>'],
								['Header 5', '<h5>'],
								['Header 6', '<h6>']
							],
							sl = styles.length,
							x = 0;
						sel.className = 'testyle';
						sel.onchange = new Function(this.n + '.ddaction(this,"formatblock")');
						for (x; x < sl; x++) {
							var style = styles[x];
							sel.options[x] = new Option(style[0], style[1])
						}
						h.appendChild(sel)
					break;
					
					default:
						if (c[id]) {
							var div = newElement("div"),
								x = c[id],
								func = x[2],
								ex, pos = x[0] * offset;
							div.className = obj.controlclass || 'tecontrol';
							div.style.backgroundPosition = '0px ' + pos + 'px';
							div.title = x[1];
							ex = func == 'a' ? '.action("' + x[3] + '",0,' + (x[4] || 0) + ')' : '.insert("' + x[4] + '","' + x[5] + '","' + x[3] + '")';
							div.onclick = new Function(this.n + (id == 'print' ? '.print()' : ex));
							div.onmouseover = new Function(this.n + '.hover(this,' + pos + ',1)');
							div.onmouseout = new Function(this.n + '.hover(this,' + pos + ',0)');
							h.appendChild(div);
							if (this.ie) {
								div.unselectable = 'on';
							}
						}
				}
			}
			this.t.parentNode.insertBefore(p, this.t);
			this.t.style.width = this.i.width + 'px';
			w.appendChild(this.t);
			w.appendChild(this.i);
			p.appendChild(w);
			this.t.style.display = 'none';
			if (obj.footer) {
				var f = newElement("div");
				f.className = obj.footerclass || 'tefooter';
				if (obj.toggle) {
					var to = obj.toggle,
						ts = newElement("div");
					ts.className = to.cssclass || 'toggle';
					ts.innerHTML = to.text || 'source';
					ts.onclick = new Function(this.n + '.toggle(0,this);return false');
					f.appendChild(ts)
				}
				if (obj.resize) {
					var ro = obj.resize,
						rs = newElement("div");
					rs.className = ro.cssclass || 'resize';
					rs.onmousedown = new Function('event', this.n + '.resize(event);return false');
					rs.onselectstart = function() {
						return false
					};
					f.appendChild(rs)
				}
				p.appendChild(f)
			}
			this.e = this.i.contentWindow.document;
			this.e.open();
			var m = '<html><head>',
				bodyid = obj.bodyid ? " id=\"" + obj.bodyid + "\"" : "";
			if (obj.cssfile) {
				m += '<link rel="stylesheet" href="' + obj.cssfile + '" />'
			}
			if (obj.css) {
				m += '<style type="text/css">' + obj.css + '</style>'
			}
			m += '</head><body' + bodyid + '>' + (obj.content || this.t.value);
			m += '</body></html>';
			this.e.write(m);
			this.e.close();
			this.e.designMode = 'on';
			this.d = 1;
			if (this.xhtml) {
				try {
					this.e.execCommand("styleWithCSS", 0, 0)
				} catch (e) {
					try {
						this.e.execCommand("useCSS", 0, 1)
					} catch (e) {}
				}
			}
		};
		
		edit.prototype.clear = function() {
			this.e.body.innerHTML = "";
			this.t.value = "";
		};
		
		edit.prototype.print = function() {
			this.i.contentWindow.print();
		};
		
		edit.prototype.hover = function(div, pos, dir) {
			div.style.backgroundPosition = (dir ? '34px ' : '0px ') + (pos) + 'px'
		};
		
		edit.prototype.ddaction = function(dd, a) {
			var i = dd.selectedIndex,
				v = dd.options[i].value;
			this.action(a, v)
		};
		
		edit.prototype.action = function(cmd, val, ie) {
			if (ie && !this.ie) {
				alert('Your browser does not support this function.')
			} else {
				this.e.execCommand(cmd, 0, val || null)
			}
		};
		
		edit.prototype.insert = function(pro, msg, cmd) {
			var val = prompt(pro, msg);
			if (val != null && val != '') {
				this.e.execCommand(cmd, 0, val)
			}
		};
		
		edit.prototype.setfont = function() {
			execCommand('formatblock', 0, hType)
		};
		
		edit.prototype.resize = function(e) {
			if (this.mv) {
				this.freeze()
			}
			this.i.bcs = TINY.cursor.top(e);
			this.mv = new Function('event', this.n + '.move(event)');
			this.sr = new Function(this.n + '.freeze()');
			if (this.ie) {
				doc.attachEvent('onmousemove', this.mv);
				doc.attachEvent('onmouseup', this.sr)
			} else {
				doc.addEventListener('mousemove', this.mv, 1);
				doc.addEventListener('mouseup', this.sr, 1)
			}
		};
		
		edit.prototype.move = function(e) {
			var pos = TINY.cursor.top(e);
			this.i.height = parseInt(this.i.height) + pos - this.i.bcs;
			this.i.bcs = pos
		};
		
		edit.prototype.freeze = function() {
			if (this.ie) {
				doc.detachEvent('onmousemove', this.mv);
				doc.detachEvent('onmouseup', this.sr)
			} else {
				doc.removeEventListener('mousemove', this.mv, 1);
				doc.removeEventListener('mouseup', this.sr, 1)
			}
		};
		
		edit.prototype.toggle = function(post, div) {
			if (!this.d) {
				var v = this.t.value;
				if (div) {
					div.innerHTML = this.obj.toggle.text || 'source'
				}
				if (this.xhtml && !this.ie) {
					v = v.replace(/<strong>(.*)<\/strong>/gi, '<span style="font-weight: bold;">$1</span>');
					v = v.replace(/<em>(.*)<\/em>/gi, '<span style="font-weight: italic;">$1</span>')
				}
				this.e.body.innerHTML = v;
				this.t.style.display = 'none';
				this.i.style.display = 'block';
				this.d = 1
			} else {
				var v = this.e.body.innerHTML;
				if (this.xhtml) {
					v = v.replace(/<span class="apple-style-span">(.*)<\/span>/gi, '$1')
						.replace(/ class="apple-style-span"/gi, '')
						.replace(/<span style="">/gi, '')
						.replace(/<br>/gi, '<br />')
						.replace(/<br ?\/?>$/gi, '')
						.replace(/^<br ?\/?>/gi, '')
						.replace(/(<img [^>]+[^\/])>/gi, '$1 />')
						.replace(/<b\b[^>]*>(.*?)<\/b[^>]*>/gi, '<strong>$1</strong>')
						.replace(/<i\b[^>]*>(.*?)<\/i[^>]*>/gi, '<em>$1</em>')
						.replace(/<u\b[^>]*>(.*?)<\/u[^>]*>/gi, '<span style="text-decoration:underline">$1</span>')
						.replace(/<(b|strong|em|i|u) style="font-weight: normal;?">(.*)<\/(b|strong|em|i|u)>/gi, '$2')
						.replace(/<(b|strong|em|i|u) style="(.*)">(.*)<\/(b|strong|em|i|u)>/gi, '<span style="$2"><$4>$3</$4></span>')
						.replace(/<span style="font-weight: normal;?">(.*)<\/span>/gi, '$1')
						.replace(/<span style="font-weight: bold;?">(.*)<\/span>/gi, '<strong>$1</strong>')
						.replace(/<span style="font-style: italic;?">(.*)<\/span>/gi, '<em>$1</em>')
						.replace(/<span style="font-weight: bold;?">(.*)<\/span>|<b\b[^>]*>(.*?)<\/b[^>]*>/gi, '<strong>$1</strong>')
				}
				if (div) {
					div.innerHTML = this.obj.toggle.activetext || 'wysiwyg'
				}
				this.t.value = v;
				if (!post) {
					this.t.style.height = this.i.height + 'px';
					this.i.style.display = 'none';
					this.t.style.display = 'block';
					this.d = 0
				}
			}
		};
		
		edit.prototype.post = function() {
			if (this.d) {
				this.toggle(1);
				return this.t.value;
			}
			return "";
		};
		
		return {
			edit: edit
		}
	}();

	TINY.cursor = function() {
		return {
			top: function(e) {
				return T$$$() ? win.event.clientY + doc.docElement.scrollTop + doc.body.scrollTop : e.clientY + win.scrollY
			}
		}
	}();

	return TINY;

})(window, document);