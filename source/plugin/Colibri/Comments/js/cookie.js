/*\
|*|
|*|  :: cookies.js ::
|*|
|*|  A complete cookies reader/writer framework with full unicode support.
|*|
|*|  https://developer.mozilla.org/en-US/docs/DOM/document.cookie
|*|
|*|  This framework is released under the GNU Public License, version 3 or later.
|*|  http://www.gnu.org/licenses/gpl-3.0-standalone.html
|*|
|*|  Syntaxes:
|*|
|*|  * docCookies.setItem(name, value[, end[, path[, domain[, secure]]]])
|*|  * docCookies.getItem(name)
|*|  * docCookies.removeItem(name[, path], domain)
|*|  * docCookies.hasItem(name)
|*|  * docCookies.keys()
|*|
\*/

var docCookies = {

	getItem: function (sKey) {
		return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
	},
	
	setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
		if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return false; }
		var sExpires = "";
		if (vEnd) {
			switch (vEnd.constructor) {
				case Number:
				sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
				break;
				case String:
				sExpires = "; expires=" + vEnd;
				break;
				case Date:
				sExpires = "; expires=" + vEnd.toUTCString();
				break;
			}
		}
		document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
		return true;
	},

	removeItem: function (sKey, sPath, sDomain) {
		if (!sKey || !this.hasItem(sKey)) { return false; }
		document.cookie = encodeURIComponent(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + ( sDomain ? "; domain=" + sDomain : "") + ( sPath ? "; path=" + sPath : "");
		return true;
	},

	hasItem: function (sKey) {
		return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
	},

	keys: /* optional method: you can safely remove it! */ function () {
		var aKeys = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "").split(/\s*(?:\=[^;]*)?;\s*/);
		for (var nIdx = 0; nIdx < aKeys.length; nIdx++) { aKeys[nIdx] = decodeURIComponent(aKeys[nIdx]); }
		return aKeys;
	}
};

/*
Writing a cookie Syntax:
	docCookies.setItem(name, value[, end[, path[, domain[, secure]]]])
	*** Create/overwrite a cookie.

-------------------------------------------------------------------
name (required)
	The name of the cookie to create/overwrite (string).

value (required)
	The value of the cookie (string).

end (optional)
	The max-age in seconds (e.g. 31536e3 for a year, Infinity for a never-expires cookie) or the expires date in GMTString format or as Date object; if not specified it will expire at the end of session (number – finite or Infinity – string, Date object or null).

path (optional)
	E.g., "/", "/mydir"; if not specified, defaults to the current path of the current document location (string or null).

domain (optional)
	E.g., "example.com", ".example.com" (includes all subdomains) or "subdomain.example.com"; if not specified, defaults to the host portion of the current document location (string or null).

secure (optional)
	The cookie will be transmitted only over secure protocol as https (boolean or null).






Getting a cookie Syntax:
	docCookies.getItem(name)
	*** Read a cookie. If the cookie doesn't exist a null value will be returned.

-------------------------------------------------------------------
name (required)
	The name of the cookie to read (string).






Removing a cookie Syntax:
	docCookies.removeItem(name[, path],domain)
	*** Delete a cookie.

-------------------------------------------------------------------
name
	the name of the cookie to remove (string).
	
path (optional)
	e.g., "/", "/mydir"; if not specified, defaults to the current path of the current document location: The path gives you the chance to specify a directory where the cookie is active. So if you want the cookie to be only sent to pages in the directory cgi-bin, set the path to "/cgi-bin". Usually the path is set to "/", which means the cookie is valid throughout the entire domain. (string or null).
	
domain (optional)
	E.g., "example.com", ".example.com" (includes all subdomains) or "subdomain.example.com"; if not specified, defaults to the host portion of the current document location (string or null).






Testing a cookie Syntax:
	docCookies.hasItem(name)
	*** Check if a cookie exists.

-------------------------------------------------------------------
name
	the name of the cookie to test (string).






Getting the list of all cookies Syntax:
	docCookies.keys()
	*** Returns an array of all readable cookies from this location.

*/