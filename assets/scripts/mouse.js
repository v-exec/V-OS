let followClose = document.getElementById("mouseClose");
let followFar = document.getElementById("mouseFar");
let cursor = document.getElementById("mouseCursor");
let icon = document.getElementById("mouseIcon");

let cursorX = -1;
let cursorY = -1;
let cCursorX = -1;
let cCursorY = -1;
let fCursorX = -1;
let fCursorY = -1;

const easeSlow = 0.1;
const easeFast = 0.2;

const closeWidth = 10;
const farWidth = 40;
const cursorWidth = 8;
const iconWidth = 25;

let reset = true;

window.requestAnimationFrame(run);

function run() {
	document.onmousemove = function (event) {
		cursorX = event.pageX;
		cursorY = event.pageY;
	}

	let dx = cursorX - cCursorX;
	cCursorX += dx * easeFast;

	let dy = cursorY - cCursorY;
	cCursorY += dy * easeFast;

	dx = cCursorX - fCursorX;
	fCursorX += dx * easeSlow;

	dy = cCursorY - fCursorY;
	fCursorY += dy * easeSlow;

	if (reset) {
		cCursorX = cursorX;
		cCursorY = cursorY;
		fCursorX = cursorX;
		fCursorY = cursorY;

		if (cursorX != -1) reset = false;
	} else {
		cursor.style.opacity = 1;
		followClose.style.opacity = 1;
		followFar.style.opacity = 1;
		icon.style.opacity = 1;
	}

	cursor.style.left = cursorX - cursorWidth / 2 + 'px';
	cursor.style.top = cursorY - cursorWidth / 2 + 'px';

	followClose.style.left = cCursorX - closeWidth / 2 + 'px';
	followClose.style.top = cCursorY - closeWidth / 2 + 'px';

	followFar.style.left = fCursorX - farWidth / 2 + 'px';
	followFar.style.top = fCursorY - farWidth / 2 + 'px';

	icon.style.left = (fCursorX - iconWidth / 2) + 15 + 'px';
	icon.style.top = (fCursorY - iconWidth / 2) - 15 + 'px';

	window.requestAnimationFrame(run);
}