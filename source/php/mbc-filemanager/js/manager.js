/**
 * @author: nereo costacurta
 *
 * @project: colibrì CMS | madebycambiamentico
 * @description: file manager main script. manage edits, upload, delete.
 *
 * @require: [jquery.js >= 1.11.3], ./manager.php
 *
 * @license: GPLv3
 * @copyright: (C)2016 nereo costacurta
 */


function printAttr(s){
	return s ? s.replace(/"/g,"&quot;").replace(/'/g,"&#039;") : '';
}
function printText(s){
	return s ? s.replace(/</g,"&lt;").replace(/>/g,'&gt;') : '';
}

var BUSY = {
	busy: true,
	dom : $('#loader'),
	start: function(){
		this.busy = true;
		this.dom.removeClass('done');
		//console.log('start')
	},
	end: function(){
		this.busy = false;
		this.dom.addClass('done');
		//console.log('end')
	}
};


//global folder stack.
var OPENED_FOLDERS = [], ACTIVE_FOLDER = 0;

//array with: htmlID and object file;
//needed in: edit file name + descr, or edit/add new folder
var LAST_EDIT_FILE = [false,false];


//lazy images load
var ACTIVE_IMAGES = [];
function isInWindow(elem){
	var rect = elem.getBoundingClientRect();
	return (
		(rect.top<=25 && rect.bottom>=window.innerHeight-25) ||		//elemento che copre tutta l'area attiva
		(rect.top>=25 && rect.top<=window.innerHeight-25) ||			//top dell'elemento tra +25 e wh-25
		(rect.bottom>25 && rect.bottom<window.innerHeight-25)			//bottom dell'elemento tra +25 e wh-25
	);
}
function checkLoadImage(){
	if (ACTIVE_IMAGES.length){
		purge = 0;
		ACTIVE_IMAGES.each(function(){
			if (isInWindow(this)){
				var self = $(this).addClass('wait').removeClass('load');
				var src = self.data('thumb');
				$('<img>').load(function(){
					self.children('label').css('background-image','url("'+src+'")');
					setTimeout(function(){ self.addClass('loaded').removeClass('wait') },30);
				}).fail(function(e){
					console.log(e)
				})[0].src = src;
				purge++
			}
		})
		if (purge) ACTIVE_IMAGES = $('#dir-'+ACTIVE_FOLDER+' .load');
	};
}
function initLazyLoad(){
	$(window).on('scroll.lazy resize.lazy',checkLoadImage);
}
if (MANAGER_OPTIONS.jsimage===1) initLazyLoad();

function getFolderHtml(f,id, dirId, prepare){//name for input radio = "md"
	//temp id
	var htmlID = 'dir_'+dirId+'-'+id;
	//create html folder
	var html = '<figure class="type-F">'+
		'<div class="icon i-folder"><label for="'+htmlID+'" class="image"></label></div>'+
		'<div class="title"><label for="'+htmlID+'">'+printText(f)+'</label></div>'+
		'<figcaption>'+
			'<input name="md" id="'+htmlID+'" type="radio" value="">'+//on first click determine new FolderClass() id and assign to <value>
			'<b class="sicon ed"><i class="pencil"></i></b>'+//onclick = folderEdit(id)
			'<b class="sicon"><i class="trash"></i></b>'+//onclick = folderDelete(id)
		'</figcaption>'+
	'</figure>';
	//--------------------------------------
	//return as html or jquery ready element
	if (prepare){
		html = $(html);
		html.find('#'+htmlID).setAsFolder();
		html.find('b.ed').fileEdit();
		//TODO...
		return html;
	}
	else return html;
}

function getFolderUpHtml(dirId,todirid, prepare){
	var htmlID = "up_"+dirId+'-'+todirid;
	var html = '<figure class="type-U">'+
		'<div class="icon i-folderup"><label for="'+htmlID+'" class="image"></label></div>'+
		'<div class="title"><label for="'+htmlID+'">'+printText(OPENED_FOLDERS[todirid].dir)+'</label></div>'+
		'<input name="md" id="'+htmlID+'" type="radio" value="'+todirid+'">'+
	'</figure>';
	//--------------------------------------
	//return as html or jquery ready element
	if (prepare){
		html = $(html);
		html.find('#'+htmlID).setAsFolder();
		//TODO...
		return html;
	}
	else return html;
}

$.fn.setAsFolder = function(){
	return this.change(function(){
		if (this.value === ""){
			var tips = this.id.match(/dir_(\d+)-(\d+)/);
			if (tips){
				var self = this;
				var pf = Number(tips[1]);//parent folder id
				var fi = Number(tips[2]);//folder id in parent stack
				var dirToScan = OPENED_FOLDERS[pf].dir + OPENED_FOLDERS[pf].folders[fi];
				loadFolder(dirToScan, pf, function(id){
					self.value = id;
				})
			}
			else console.log(tips);
		}
		else{
			var id = Number(this.value);
			OPENED_FOLDERS[id].enable();
		}
	})
}

function newFolder(){
	LAST_EDIT_FILE = [false,false];
	$('#ef-type').val("newdir");
	$('#ef-dir').val(OPENED_FOLDERS[ACTIVE_FOLDER].dir);
	$('#ef-title').val('');
	//open dialog
	$('#edit-files').removeClass('file').addClass('folder').modalbox({maxH: 260});
	$('#saveAllEdits').off().one('click',function(){
		editFile(ACTIVE_FOLDER);
	});
}



function getFileHtml(f,id, dirId, prepare){
	//temp id. NB - label with image background has id = L+htmlID.
	var htmlID = 'file_'+dirId+'-'+f.g+'-'+id;
	//get file name without extension (foo.bar -> foo)
	var purename = f.f.substring(0,f.f.length-f.e.length-1);
	//create html file
	var html = '<figure class="file type-'+f.g+'">';
	//icon or image? if image: load or set background?
	if (f.g===0)
		html += '<div class="icon i-'+f.e + (MANAGER_OPTIONS.jsimage===1 ? ' load" data-thumb="'+printAttr(OPENED_FOLDERS[dirId].turl+f.f) : '')+'">'+
			'<label for="'+htmlID+'" class="image" id="L'+htmlID+'"'+
				(MANAGER_OPTIONS.jsimage===0 ? 'style="background:url(\''+printAttr(OPENED_FOLDERS[dirId].turl+f.f)+'\')"' : '')+
			'></label>'+
		'</div>';
	else
		html += '<div class="icon i-'+f.e+'"><label for="'+htmlID+'" class="image"></label></div>';
	//add caption and editor buttons
	html += '<div class="title"><label for="'+htmlID+'">'+printText(purename)+'</label></div>'+
		'<figcaption>'+
			'<input id="'+htmlID+'" data-file="'+printAttr(f.f)+'" type="checkbox">'+					//on first click determine database id and assign it to <value>
			'<a class="sicon" href="'+printAttr(OPENED_FOLDERS[dirId].url+f.f)+'" target="_blank"><i class="download"></i></a>';
			switch(f.g){
				case 0: html += '<b class="sicon pw"><i class="search"></i></b>'; break;				//onclick = previewImage(id)
				case 2: html += '<b class="sicon pw"><i class="video-camera"></i></b>'; break;		//onclick = previewVideo(id)
				case 3: html += '<b class="sicon pw"><i class="audio-2"></i></b>'; break;				//onclick = previewAudio
			} 
	html += '<b class="sicon ed"><i class="pencil"></i></b>'+			//onclick = fileEdit(id)... (eg. name, description...)
			'<b class="sicon del"><i class="trash"></i></b>'+				//onclick = fileDelete(id)
		'</figcaption>'+
	'</figure>';
	//--------------------------------------
	//return as html or jquery ready element
	if (prepare){
		html = $(html).setFigureTitle();
		html.find('#'+htmlID).setAsFile();
		html.find('b.pw').filePreview();
		html.find('b.del').fileDelete();
		html.find('b.ed').fileEdit();
		return html;
	}
	else return html;
}


$.fn.setAsFile = function(){
	return this.change(function(){
		$(this).parents('figure.file').toggleClass('selected');
		var tips = this.id.match(/file_(\d+)-(\d+)-(\d+)/);
		if (tips){
			var pf = Number(tips[1]);//parent folder id
			var group = Number(tips[2]);//file group id in parent stack
			var fi = Number(tips[3]);//file id in parent stack
			MANAGER_OPTIONS.onChange((this.checked ? "selectFile" : "unselectFile"), OPENED_FOLDERS[pf].files[group][fi], OPENED_FOLDERS[pf].dir);
		}
	});
}

function clearSel(){
	$('figure.selected input').click();
}

$.fn.setFigureTitle = function(){
	return this.each(function(){
		$(this).attr( 'data-title', $(this).find('.title label').text().toLowerCase() );
	});
}


$.fn.filePreview = function(){
	return this.click(function(){
		var input = $(this).parent().find('input');
		if (!input.length) return false;
		var tips = input[0].id.match(/file_(\d+)-(\d+)-(\d+)/);
		if (tips){
			var pf = Number(tips[1]);//parent folder id
			var group = Number(tips[2]);//file group id in parent stack
			var fi = Number(tips[3]);//file id in parent stack
			//console.log("parent-folder "+pf+" group "+group+" file id "+fi)
			var myfile = OPENED_FOLDERS[pf].url + OPENED_FOLDERS[pf].files[group][fi].f;
			switch(group){
				case 0:
					//images
					//TODO
				break;
				case 2:
					//video
					$('#video-preview .video-wrapper').empty();
					$('#video-preview').modalbox();
					$('<video id="video-player" style="width:95%;height:95%" src="'+printAttr(myfile)+'" preload="false"></audio>')
						.appendTo('#video-preview .video-wrapper')
						.mediaelementplayer({
							success: function(media,dom){
								media.play();
							},
							error: function(){
								$('#video-preview .video-wrapper').empty();
								$('#video-preview').modalbox();
								alert("Ooops! Il formato del video non è supportato!");
							}
						});
				break;
				case 3:
					//audio
					$('#audio-preview .audio-wrapper').empty();
					$('#audio-preview').modalbox();
					$('<audio id="audio-player" src="'+printAttr(myfile)+'" preload="false"></audio>')
						.appendTo('#audio-preview .audio-wrapper')
						.mediaelementplayer({
							audioWidth: ($(window).width() < 480 ? 300 : 420),
							success: function(media,dom){
								media.play();
							},
							error: function(){
								$('#audio-preview .audio-wrapper').empty();
								$('#audio-preview').modalbox();
								alert("Ooops! Il formato dell'audio non è supportato!");
							}
						});
				break;
				default:
			}
		}
		else console.log(input[0].id);
	});
}

$.fn.fileEdit = function(){
	return this.click(function(){
		var input = $(this).parent().find('input');
		if (!input.length) return false;
		var tips = input[0].id.match(/file_(\d+)-(\d+)-(\d+)/);
		if (tips){
			//-------------------------- FILE
			var pf = Number(tips[1]);//parent folder id
			var group = Number(tips[2]);//file group id in parent stack
			var fi = Number(tips[3]);//file id in parent stack
			//console.log("parent-folder "+pf+" group "+group+" file id "+fi)
			//TODO
			if (LAST_EDIT_FILE[0] !== input[0].id){
				LAST_EDIT_FILE[0] = input[0].id;
				LAST_EDIT_FILE[1] = OPENED_FOLDERS[pf].files[group][fi];
				var file = LAST_EDIT_FILE[1].f;
				$('#ef-type').val("file");
				$('#ef-dir').val(OPENED_FOLDERS[pf].dir);
				$('#ef-orig-title').val(LAST_EDIT_FILE[1].f);
				$('#ef-title').val(LAST_EDIT_FILE[1].f.replace(/\.[a-z0-9]+$/i,""));
				$('#ef-ext').html('.'+LAST_EDIT_FILE[1].f.split('.').pop());
				//***
				//description only for images...
				if (group === 0){
					var efd = $('#ef-desc').show()
					if (LAST_EDIT_FILE[1].desc === undefined){
						efd.val('').prop('disabled',true);
						//lock form
						BUSY.start();
						$.get('get-file-desc.php',{dir:OPENED_FOLDERS[pf].dir, f:LAST_EDIT_FILE[1].f},null,'json')
							.done(function(json) {
								console.log(json)
								if (json.error) return alert(json.error);
								efd.val(json.desc);
								LAST_EDIT_FILE[1].desc = json.desc;//remember description for nex time, if user want to edit again in same session
							})
							.fail(function(e) {
								console.log(e);
							})
							.always(function() {
								//unlock form
								BUSY.end();
								efd.prop('disabled',false);
							});
					}
					else{
						efd.val(LAST_EDIT_FILE[1].desc);
					}
				}
				else $('#ef-desc').val('').hide();
				//end description handler
				//***
			}
			//open dialog
			$('#edit-files').removeClass('folder').addClass('file').modalbox({maxH: (group===0 ? 390 : 260)});
			$('#saveAllEdits').off().one('click',function(){
				editFile(pf,group,fi);
			});
		}
		else{
			tips = input[0].id.match(/dir_(\d+)-(\d+)/);
			if (tips){
				//-------------------------- FOLDER
				var pf = Number(tips[1]);//parent folder id
				var fi = Number(tips[2]);//folder id in parent stack
				//console.log("parent-folder "+pf+" folder id "+fi)
				//TODO
				LAST_EDIT_FILE = [false,false];
				$('#ef-type').val("dir");
				$('#ef-dir').val(OPENED_FOLDERS[pf].dir);
				$('#ef-orig-title').val(LAST_EDIT_FILE[1].f);
				$('#ef-title').val(OPENED_FOLDERS[pf].folders[fi]);
				//open dialog
				$('#edit-files').removeClass('file').addClass('folder').modalbox({maxH: 260});
				$('#saveAllEdits').off().one('click',function(){
					editFile(pf,fi);
				});
			}
			else console.log(input[0].id);
		}
	});
}

$.fn.fileDelete = function(){
	return this.click(function(){
		if (!confirm("Vuoi veramente cancellare questo file?")) return false;
		var input = $(this).parent().find('input');
		if (!input.length) return false;
		var tips = input[0].id.match(/file_(\d+)-(\d+)-(\d+)/);
		if (tips){
			var pf = Number(tips[1]);//parent folder id
			var group = Number(tips[2]);//file group id in parent stack
			var fi = Number(tips[3]);//file id in parent stack
			//console.log("parent-folder "+pf+" group "+group+" file id "+fi)
			deleteFiles([{
				data:		{
					id : input[0].id,
					pf : pf,
					g : group,
					i : fi
				},
				f:			OPENED_FOLDERS[pf].files[group][fi].f,
				dir:		OPENED_FOLDERS[pf].dir
			}]);
		}
		else console.log(input[0].id);
	});
}

function deleteAllCheckedFiles(){
	if (!confirm("Si stanno per cancellare tutti i files selezionati (anche nelle altre cartelle). Vuoi veramente procedere?")){
		if (!confirm("Vuoi cancellare solo i file in questa cartella?")) return false;
		//delete only files in current folder:
		$('#dir-'+ACTIVE_FOLDER+' .file input:checked').deleteAllCheckedFiles();
		return true;
	}
	$('#manager_files .file input:checked').deleteAllCheckedFiles();
	return true;
}

$.fn.deleteAllCheckedFiles = function(){
	var aFiles = new Array(this.length);
	var tips;
	//prepare all files to be deleted
	this.each(function(i){
		tips = this.id.match(/file_(\d+)-(\d+)-(\d+)/);
		if (tips){
			var pf = Number(tips[1]);//parent folder id
			var group = Number(tips[2]);//file group id in parent stack
			var fi = Number(tips[3]);//file id in parent stack
			aFiles[i] = {
				data:		{
					id : this.id,
					pf : pf,
					g : group,
					i : fi
				},
				f:			OPENED_FOLDERS[pf].files[group][fi].f,
				dir:		OPENED_FOLDERS[pf].dir
			};
		}
	});
	//send request
	deleteFiles(aFiles);
	return this;
}

function deleteFiles(aFiles){
	//aFiles contains: {data: <html id>, f: <file name>, dir: <directory path from uploads>}
	
	//(a confirmation alert should already have been fired)
	
	//lock form...
	BUSY.start();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		data: {files: aFiles},
		url: 'delete-files.php'
	})
	.done(function(json){
		console.log(json);
		if (json.error) return alert("ERRORE:\n"+json.error);
		$.each(json.done,function(i,v){
			//remove html
			$('#'+v.data.id).parents('figure.file').remove();
			//opener callback
			//set null properties in files array
			OPENED_FOLDERS[v.data.pf].files[v.data.g][v.data.i] = null;
		})
		MANAGER_OPTIONS.onChange("deleteFiles", json.done);
		if (json.fail.length){
			var fail = "Attenzione!!! I seguenti file non sono stati rimossi o sono stati rimossi solo parzialmente:";
			$.each(json.fail,function(i,v){
				fail += "\n- "+v.f+" ("+v.e+")";
			});
			alert(fail)
		}
		//call sitemap generator
		$.get('../../sitemap-generator-generic.php').always(function(e){console.log(e)});
	})
	.fail(function(e){
		console.log(e);
		alert("Oooops!");
	})
	.always(function(){
		//unlock form...
		BUSY.end();
	});
}


/*
* edit file, then update html and arrays
* accept:
*		parent folder, file group, file position in array
*		parent folder, folder position in array
*/
function editFile(pf,gi,fi){
	if (pf === undefined) return false;
	//lock form
	BUSY.start();
	$.get('update-file.php',$('#my-editor').serialize(),null,'json')
		.done(function(json) {
			console.log(json)
			if (json.error) return alert(json.error);
			switch(json.action){
				case 'newdir':
					OPENED_FOLDERS[pf].addFolder(json.f)
				break;
				case 'dir'://pf = parent folder, gi = index in folders[...]
					OPENED_FOLDERS[pf].folders[gi] = json.f;
					//update folder and folder-up
					$('figure.type-F .title>label[for="dir_'+pf+'-'+gi+'"]').text(json.f);
					$('#up_'+ $('#dir_'+pf+'-'+gi).val() +'-'+pf).text(json.f);
				break;
				case 'file':
					OPENED_FOLDERS[pf].files[gi][fi].f = json.f;
					$('figure.file .title>label[for="file_'+pf+'-'+gi+'-'+fi+'"]').text(json.f.replace(/\.[a-z0-9]+$/i,""));
					if (json.desc !== undefined) OPENED_FOLDERS[pf].files[gi][fi].desc = json.desc;
				break;
				default:
					return console.log(json);
			}
			//close modal box
			$('#edit-files').modalbox();
			//call sitemap generator
			$.get('../../sitemap-generator-generic.php').always(function(e){console.log(e)});
		})
		.fail(function(e) {
			console.log(e);
		})
		.always(function() {
			//unlock form
			BUSY.end();
		});
}


function FolderClass(){
	var self = this;
	self.url = '';
	self.folders = [];
	self.files = [
		/*img*/[],
		/*file*/[],
		/*video*/[],
		/*music*/[],
		/*archives*/[]
	];
	self.active = false;//if active then when window scrolls it will check image to show (lazy load)
	//when first opened, unactive all folders and add this.
	self.ID = OPENED_FOLDERS.length;
	self.parentID = self.ID
	OPENED_FOLDERS.push(self);
	self.dom = $('<div class="albums" id="dir-'+self.ID+'">');//wrapper for this folder. contains: sub-folders and files (<figure>)
	
	//order files by group (total of 5 types)
	function orderFiles(files){
		$.each(files,function(){
			self.files[this.g].push(this);
		})
	}
	
	function addCurrentFolders(){
		var html = "";
		$.each(self.folders,function(i){
			html += getFolderHtml(this,i, self.ID, false);
		});
		if (!html) return false;
		self.dom.append(html);
		return true;
	}
	self.addFolder = function(f){
		var i = self.folders.push(f) -1;
		var figures = self.dom.find('figure.type-F').eq(0);
		var folder = getFolderHtml(f,i, self.ID, true);
		if (figures.length)
			figures.before(folder);
		else{
			figures = self.dom.find('figure.type-U').last();
			if (figures.length)
				figures.after(folder);
			else
				self.dom.prepend(folder);
		}
	}
	
	function addCurrentFiles(){
		var html = "";
		$.each(self.files,function(){
			//each group...
			$.each(this,function(i){
				html += getFileHtml(this,i, self.ID, false);
			})
		});
		if (!html) return false;
		self.dom.append(html);
		return true;
	}
	self.addFiles = function(ff){
		console.log("adding files...");
		console.log(ff);
		//ff can contains 'insert' or 'update'. addfile() automatically divide the two things
		$.each(ff,function(i,v){
			self.addFile(v);
		});
		//now i should call opener function...
		MANAGER_OPTIONS.onChange("addFiles", ff);
	};
	self.addFile = function(f){
		//new file
		if (f.db === 'insert'){
			//console.log("INSERT file..."); console.log(f);
			var i = self.files[f.g].push(f) -1;
			var figures = self.dom.find('figure.file').eq(0);
			if (figures.length) figures.before( getFileHtml(f,i, self.ID, true) );
			else self.dom.append(getFileHtml(f,i, self.ID, true));
			if (f.g === 0){
				//show image
				ACTIVE_IMAGES = $('#dir-'+self.ID+' .load');
				$(window).trigger('resize.lazy');
			}
			return true;
		}
		//updated file (only content --- editing name is in another function!!!)
		else{
			//console.log("UPDATE file..."); console.log(f);
			if (f.g === 0){
				$.each(self.files[0],function(i,v){
					if (v.f === f.f){
						//should refresh background image...
						$('#Lfile_'+self.ID+'-'+f.g+'-'+i).css('background-image',self.turl+f.f+'?'+(new Date().getTime()));
						//console.log("image updated!");
						//stop each recursion
						return false;
					}
				});
			}
			return true;
		}
	}
	
	//functions to show/hide folder. doesn't move around the folder tree
	//1: open folder
	self.enable = function(othersAlreadyDisabled){
		if (!othersAlreadyDisabled) $.each(OPENED_FOLDERS,function(){
			this.disable();
		});
		self.dom.addClass('active');
		//add array of elements to check windows scroll.
		self.active = true;
		ACTIVE_FOLDER = self.ID;
		ACTIVE_IMAGES = $('#dir-'+self.ID+' .load');
		checkLoadImage();
	}
	//1: close folder
	self.disable = function(){
		if (!self.active) return false;
		self.active = false;
		self.dom.removeClass('active');
		return true;
	}
	//move up into the tree folder
	self.up = function(){
		if (!self.parentID) return self.ID;
		self.disable();
		OPENED_FOLDERS[self.parentID].enable(true);
		return self.parentID;
	}
	
	//------------------------------------
	//create all folders and put into html
	self.init = function(obj,parentID){
		self.dir = obj.scanned_dir
		self.url = obj.uploads_dir + self.dir;
		self.turl = obj.thumbs_dir + self.dir;
		//set files/folders
		self.folders = obj.folders;
		orderFiles(obj.files);
		//prepare html and put it into dom
		if (parentID !== undefined){
			self.parentID = Number(parentID);
			//create "go-up" tile
			self.dom.append(getFolderUpHtml(self.ID,self.parentID, true));
		}
		addCurrentFolders();
		addCurrentFiles();
		self.dom.appendTo('#manager_files');
		//initialize elements
		$('#dir-'+self.ID+'>figure').setFigureTitle();
		$('#dir-'+self.ID+'>.type-F input').setAsFolder();
		$('#dir-'+self.ID+'>.file input').setAsFile();
		$('#dir-'+self.ID+' b.pw').filePreview();
		$('#dir-'+self.ID+' b.del').fileDelete();
		$('#dir-'+self.ID+' b.ed').fileEdit();
		//open (aka show)
		self.enable();//if just added to DOM, any transition may not occurs...
		return self;
	}
	
	return self;
}


function loadFolder(dir,openfromid,callback){
	//for file filters see config.php | get-file-list.php
	//lock form...
	BUSY.start();
	$.ajax({
		type: 'GET',
		dataType: 'json',
		data: {folder: dir, filter: MANAGER_OPTIONS.filter},
		url: 'get-file-list.php'
	})
	.done(function(e){
		console.log(e);
		if (e.error) return alert("ERRORE:\n"+e.error);
		var newid = new FolderClass().init(e,openfromid).ID;
		if (callback) callback(newid)
	})
	.fail(function(e){
		console.log(e);
		alert("Oooops!");
	})
	.always(function(){
		//unlock form...
		BUSY.end();
	});
}


var searchAndFilterTool = {
	//search by title
	search : function(s){
		if (!s){
			//reset search
			$('#manager_files').removeClass("searching");
			$('#manager_files figure').removeClass('found');
			checkLoadImage();
			return true;
		}
		else{
			if (s.length === 1) $('#manager_files').addClass('searching');
			$('#manager_files figure').removeClass('found');
			checkLoadImage();
			return $('#manager_files figure[data-title*="'+s.replace(/"/g,'\"')+'"]').addClass("found").length;
		}
	},
	//filter by group (image / file / archive...)
	filter : function(group){
		$('#manager_files').toggleClass('t-'+group);
		checkLoadImage();
		/*if ($('#manager_files').attr('class').match(/t-/g)) $('#manager_files').addClass('filtering');
		else $('#manager_files').removeClass('filtering');*/
	}
}

$.fn.searchTool = function(){
	return this.keyup(function(){
		if (searchAndFilterTool.search(this.value.toLowerCase())) $('#search-icon').removeClass('problem');
		else $('#search-icon').addClass('problem');
	})
}
$.fn.filterTool = function(){
	return this.change(function(){
		searchAndFilterTool.filter(this.value);
		$(this).next().find('i').toggleClass('checkboard');
	})
}



//------------------------------------------------

$(function(){
	//support for 3dtransform
	if (!(function(){
		var el = $('<p>').css({
			'webkitTransform':'translateZ(1px)',
			'transform':'translateZ(1px)'
			//add other vendor prefixed properies if you want...
			//according to your css style sheet!
		});
		var has3d = el.css('-webkit-transform') || el.css('transform');
		return (has3d !== undefined && has3d.length > 0 && has3d !== "none");
	})()){
		$('body').addClass('no3d');
	};
	
	//-------------------------
	//popups (modal boxes)
	
	//audio
	$('#audio-preview').modalbox({maxH: 260},{
		close: function(){
			var audio = $('#audio-preview audio')[0];
			if (audio) audio.player.pause();
		}
	},true);
	
	//video
	$('#video-preview').modalbox(null,{
		close: function(){
			var video = $('#video-preview video')[0];
			if (video) video.player.pause();
		}
	},true);
	
	//upload
	$('#upload-files').modalbox(null,{
		close: function(){
			setTimeout(function(){
				if (myDropzone.files.length) myDropzone.removeAllFiles();
			},
			300);
		}
	},true);
	
	//-------------------------
	//popup triggers...
	
	$('#mt-new-folder').click(newFolder);
	$('#my-editor').submit(function(e){
		e.preventDefault();
	})
	
	$('#mt-open-upload').click(function(){
		//update directory
		$('#uf-dir').val( OPENED_FOLDERS[ACTIVE_FOLDER].dir );
		//open file upload manager
		$('#upload-files').modalbox();
	});

	//-------------------------
	//search and filtering tools
	$('#search').searchTool();
	$('#mt-open-filters').click(function(){
		$('#filters').toggleClass("active");
	});
	$('#filters input').filterTool();
	//update if global filter given
	if (MANAGER_OPTIONS.filter !== 'all'){
		var filtered = MANAGER_OPTIONS.getIntFilter();
		$('#filters input').each(function(i){
			if (i !== filtered) $(this).prop('checked',false)
				.next().find('i').toggleClass('checkboard');
		});
	}
	
	//delete
	$('#mt-delete-all').click(deleteAllCheckedFiles);
	
});