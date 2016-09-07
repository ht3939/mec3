// プロトコルを判定
if (window.location.protocol == "http:") {
	document.write('<script type="text/javascript" src="http://ajaxzip3.googlecode.com/svn/trunk/ajaxzip3/ajaxzip3.js"></script>');
} else if (window.location.protocol == "https:") {
	document.write('<script type="text/javascript" src="https://ajaxzip3.googlecode.com/svn/trunk/ajaxzip3/ajaxzip3-https.js"></script>');
}