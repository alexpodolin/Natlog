
/*function getRegForma() {
	initModPupup(true, false, true);
	var okno = document.getElementsByClassName('okno')[0],
		url = '/ajax/get_reg_content.php?' + new Date().getTime(),
		req = createRequest();
	req.open("GET", url, true); req.send(null);
	req.onreadystatechange = function() {
		if (req.readyState == 4) {
			if (req.status == 200) {
				okno.innerHTML = req.responseText;
				okno.className += ' okno-float-top';
				modOknoShow(100, 0, true, false);
				window.onresize = function() { modOknoShow(100, 0, true, false); }
				jQuery(function($) {
					$.mask.definitions['~']='[+-]';
					$('#phone').mask('+7 (999) 999-99-99');
				});
			} else {
				console.log("Ошибка на сервере. " + req.status);
			}
		}
	}
}
*/
 
jQuery(function($) {
	$.mask.definitions['~']='[:]';
	$('#time-from').mask('00:00:00');
});