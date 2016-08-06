/**
* wrap text to fixed width.
*
* @param (string) txt The string to wrap
* @param (object) options Contains othe next options:
*    @param (int)  width        Maximum length of every row. default: 100
*    @param (char) tab          The char used for padding. default: " "
*    @param (bool) breakwords   If true: words are broken. Else words cannot be broken and will go to new line.
*                               This can lead to issues if the word is longer than <width>. default: false
*
* @author Nereo Costacurta
* @license MIT
*/

//polyfill of Object.assign (ECMAScript 6)
//(https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign#Polyfill)
if (typeof Object.assign != 'function') {
	Object.assign = function(target) {
		'use strict';
		if (target == null) {
			throw new TypeError('Cannot convert undefined or null to object');
		}

		target = Object(target);
		for (var index = 1; index < arguments.length; index++) {
			var source = arguments[index];
			if (source != null) {
				for (var key in source) {
					if (Object.prototype.hasOwnProperty.call(source, key)) {
						target[key] = source[key];
					}
				}
			}
		}
		return target;
	};
}

function textWrapper(txt, options) {
	var opt = {
		width : 100,
		tab : ' ',
		breakWord : false
	};
	Object.assign(opt, options);//extends opt with options
	
	function noWordsLeftBehind(txt,nextchar){
		if (!nextchar || /\s/.test(nextchar)) return [txt, ''];
		for (var i=txt.length-1;i>0;i--){
			if (/\s/.test(txt[i])){
				return [txt.substr(0,i), txt.substr(i+1)];
			}
		}
		return [txt, ''];
	}
	
	var pads = 0;
	
	for (var i = 0, j = 0; i < txt.length; i++, j++) {
		//keep track of initial padding (spaces)
		if (pads == j && txt[i] == opt.tab) {
			pads++;
		}
		//skip \r;
		if (txt[i] == "\r") {
			--j;
			continue;
		}
		//if \n reset wrapping;
		else if (txt[i] == "\n") {
			//reset count
			pads = 0;
			j = -1;
			continue;
		}
		//if character and 
		else if (j == opt.width) {
			//new line, add pads, reset.
			var pre = txt.substr(0, i);
			var padd = opt.tab.repeat(pads);
			var post = txt.substr(i);
			if (opt.breakWord){
				pre += "\n";
			}
			else{
				//no half words should be left behind...
				var pre_parts = noWordsLeftBehind(pre,post[0]);
				pre = pre_parts[0] + "\n";
				post = pre_parts[1].trim() + post.trim();
			}
			if (padd && post.substr(0, pads) !== padd) {
				//add padding
				txt = pre + padd + post;
				j = pads - 1;
				i = (pre + padd).length;
			} else {
				//do not add padding
				txt = pre + post;
				j = -1;
			}
			continue;
		}
	}
	return txt
}