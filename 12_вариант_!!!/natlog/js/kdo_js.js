function gotoAllBrPageSelect(el) {
	window.location.href = el.value;
}

function getAddressDetailById(id) {
	var okno = initModPupup(true, true, true),
		url = '/ajax/get_address_details_by_id.php?' + new Date().getTime() + '&id=' + id,
		req = createRequest();
	progressInEl(okno);
	modOknoShow(false, false, true, true);
	req.open("GET", url, true); req.send(null);
	req.onreadystatechange = function() {
		if (req.readyState == 4) {
			if (req.status == 200) {
				okno.innerHTML = req.responseText;
				modOknoShow(false, false, true, true);
				window.onresize = function() { modOknoShow(false, false, true, true); }
			} else {
				console.log("Ошибка на сервере. " + req.status);
			}
		}
	}
}

function getAddressDetails(el) {
	var okno = initModPupup(true, true, true),
		vrf = el.getAttribute('data-vrf'),
		ip = el.getAttribute('data-ip'),
		url = '/ajax/get_address_details.php?' + new Date().getTime() + '&ip=' + ip + '&vrf=' + vrf,
		req = createRequest();
	progressInEl(okno);
	modOknoShow(false, false, true, true);
	req.open("GET", url, true); req.send(null);
	req.onreadystatechange = function() {
		if (req.readyState == 4) {
			if (req.status == 200) {
				okno.innerHTML = req.responseText;
				modOknoShow(false, false, true, true);
				window.onresize = function() { modOknoShow(false, false, true, true); }
			} else {
				console.log("Ошибка на сервере. " + req.status);
			}
		}
	}
}

function goSearchAddress() {
	var searchForm = document.getElementsByClassName('kdo-main')[0].getElementsByTagName('form')[0];
	searchAddress(searchForm)
}

function searchAddress(el) {
	var searchStr = el.getElementsByTagName('input')[0].value.trim(),
		searchTypeAddress = el.getElementsByTagName('input')[1].checked ? el.getElementsByTagName('input')[1].value : '',
		searchTypeName = el.getElementsByTagName('input')[2].checked ? el.getElementsByTagName('input')[2].value : '',
		formAnswer = document.getElementById('form-answer'),
		url = '/ajax/search_address.php?' + new Date().getTime() + '&search_str=' + searchStr + '&searchTypeAddress=' + searchTypeAddress + '&searchTypeName=' + searchTypeName,
		req = createRequest();
	if (searchStr) {
		progressInEl(formAnswer);
		formAnswer.className += ' text-center';
		req.open("GET", url, true); req.send(null);
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				if (req.status == 200) {
					formAnswer.className = formAnswer.className.replace(' text-center', '');
					formAnswer.innerHTML = request.responseText;
				} else {
					console.log("Ошибка на сервере. " + req.status);
				}
			}
		}		
	}
}

function initModPupup(over, overClosed, shadowed) {
	//over (true или false) - наличие слоя по всему экрану. Нужно для закрытия всплывающего окна по клику на любой области экрана и создания эффекта затемнения.
	//overClosed (true или false) - закрытие всплывающего окна по клику на любой области экрана.
	//shadowed (true или false) - эффект затемнения.
	var okno = document.getElementsByClassName('okno')[0];
	if (!okno) {
		var okno = document.createElement('div');
		okno.className = 'okno';
		document.body.appendChild(okno);
		if (over) {
			var over = document.createElement('div');
			over.className = 'overhead';
			if (overClosed) {
				over.onmouseup = function () { closeModPupup(); }
				over.className += ' overhead-closed';
			}
			if (shadowed) {
				over.className += ' overhead-shadow';
			}
			document.body.appendChild(over);
		}	
	}
	return okno;
}

function closeModPupup() {
	var okno = document.getElementsByClassName('okno')[0],
		overhead = document.getElementsByClassName('overhead')[0];
	if (okno) {
		overhead.parentNode.removeChild(overhead);
		okno.parentNode.removeChild(okno);		
	}
}

function modOknoShow(t, l, paddTop, overClosed) {
	//t (число или false) - верхняя точка размещения окна. Если false, 0.
	//l (число или false) - левая точка размещения окна. Если false, 0.
	//paddTop (true или false) - учитывать ли то, что экран может быть проскролин вниз. В этом случае верхняя точка считается относительно относительной верхней границы.
	//overClosed (true или false) - если false, необходимо создать кнопку с закрытием.
	var okno = document.getElementsByClassName('okno')[0];
	if (!overClosed) {
		close = document.createElement('div');
		close.innerHTML = '?';
		close.className = 'popup-close-button';
		close.onclick = function() { closeModPupup(); }
		okno.appendChild(close);
	}
	if (!l) {
		var l = (window.innerWidth/2) - (okno.offsetWidth/2);
	}
	test(okno.offsetWidth);
	if (!t) {
		var t = (window.innerHeight/2) - (okno.offsetHeight/2);
		t = t - 20;
	}
	t = t > 0 ? t : 0;
	l = l > 0 ? l : 0;
	okno.style.height = 'auto';
	if (paddTop) {
		topScreen = (document && document.scrollTop  || document.body && document.body.scrollTop  || document.body && document.documentElement.scrollTop || 0);
	} else {
		topScreen = 0;
	}
	okno.style.top = topScreen + t + 'px';
	okno.style.left = l + 'px';
}

function progressInEl(el) {
	el.innerHTML = '<img src="/img/other/progress.gif">';
}
function progressInId(id) {
	document.getElementById(id).innerHTML = '<img src="/img/other/progress.gif">';
}
function progressInIdStop(id) {
	document.getElementById(id).innerHTML = '';
}

function test(ms) {
	message = ms ? ms : 'test ok';
	console.log(message);
}

var request = null;
function createRequest() {
	try {
		request = new XMLHttpRequest();
	} catch (trymicrosoft) {
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (othermicrosoft) {
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (failed) {
				request = null;
			}
		}
	}
	if (request == null) {
		alert("Error creating request object!");
	} else {
		return request;
	}
}