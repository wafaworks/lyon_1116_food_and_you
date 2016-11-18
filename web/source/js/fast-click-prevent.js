$(document).on('mouseup', 'input,a,button,.btn', function (e) {
	var node = $(this);
	setTimeout(function () { node.css('pointer-events', 'none'); }, 10);
	var er;try { clearTimeout(node.timerTMO); } catch(er) {};
	node.timerTMO = setTimeout(function () { node.css('pointer-events', ''); }, 500);
});
