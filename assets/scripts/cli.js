//globals
var cli = document.getElementById('cli');

var outputBox = document.getElementById('lo-cli-output');
var inputBox = document.getElementById('lo-cli-input');

var username;
var loc;
var input;

//responses
var noNameGreetings = [
"Hi, I'm LOGO. I can't seem to find you in my logs, what's your name?",
"Hello, my name's LOGO. I can't seem find you in my records, what would you like me to call you?",
"Welcome traveller, I'm LOGO. What's your name?"
];

var nameGreetings = [
"Hi #[user], welcome back to V-OS. If there's anything I can do, just write <b>help</b> for a list of commands.",
"Welcome back, #[user]. Write <b>help</b> if you need any assistance.",
"Hello #[user], your return is welcome."
];

var introductions = [
"Nice to meet you, #[user]. I am V-OS' guide. If there's anything you need help with, just write <b>help</b>.",
"Welcome, #[user]. I am the guide for this construct world. If you need help, just write <b>help</b>."
];

var rename = "Okay, I'll call you #[user] from now on.";

var commands = [
"<i><b>help</b></i> - get list of commands<br><br>",
"<i><b>index</b></i> - get list of navigation pages<br><br>",
"<i><b>travel page_name</b></i> - travel to page <i>page_name</i><br><br>",
"<i><b>rename new_name</b></i> - change name to <i>new_name</i><br><br>",
"Alternatively, you may also write the first letter of each command as shorthand."
];

var help = 'V-OS is a wiki site with over 100 unique pages.<br><br>It is navigated primarily through links (bold text).<br><br>This sidebar also features related pages to the one you are currently visiting.<br><br>Here are the commands I can handle:<br><br>';
for (var i = 0; i < commands.length; i++) help += commands[i];

var indexFail = "There seemed to be an error fetching the indices, my apologies.";
var travelFail = "The location you are trying to travel to does not appear to exist.";

//runtime
intro();

//main
cli.addEventListener("click", function() {
	inputBox.focus();
});

inputBox.onpaste = function(e) {
	e.preventDefault();
}

function intro() {
	username = getCookie("username");

	if (username == "") username = null;

	if (username != null) outputText(replaceUsername(nameGreetings[getRandomInt(0, nameGreetings.length)]));
	else outputText(noNameGreetings[getRandomInt(0, noNameGreetings.length)]);
}

function guide(e) {
	if (e.keyCode == 13) {
		e.preventDefault();
		input = inputBox.value;
		inputBox.value = null;

		if (username == null && input.length > 0) {
			username = input;
			document.cookie = "username=" + username +"; expires=Fri, 31 Dec 2030 23:59:59 GMT;";
			outputText(replaceUsername(introductions[getRandomInt(0, introductions.length)]));

		} else if (input.toLowerCase().startsWith("help")) {
			outputText(help);

		} else if (input.toLowerCase().startsWith("h") && input.trim().length == 1) {
			outputText(help);

		} else if (input.toLowerCase().startsWith("rename")) {
			var newName = input.substring(6, input.length).trim();
			if (newName == "") outputText("I need you to write the new name you want me to call you.");
			else {
				//sanitize string
				newName = encodeHTML(newName);
				username = newName;
				document.cookie = "username=" + username +"; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
				outputText(replaceUsername(rename));
			}

		} else if (input.toLowerCase().startsWith("r ")) {
			var newName = input.substring(2, input.length).trim();
			if (newName == "") outputText("I need you to write the new name you want me to call you.");
			else {
				//sanitize string
				newName = encodeHTML(newName);
				username = newName;
				document.cookie = "username=" + username +"; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
				outputText(replaceUsername(rename));
			}

		} else if (input.toLowerCase().startsWith("index")) {
			index();

		} else if (input.toLowerCase().startsWith("i") && input.trim().length == 1) {
			index();

		} else if (input.toLowerCase().startsWith("travel")) {
			var destination = input.substring(6, input.length).trim();
			travel(destination);

		} else if (input.toLowerCase().startsWith("t ")) {
			var destination = input.substring(2, input.length).trim();
			travel(destination);

		} else {
			outputText("Write <b>help</b> for a list of valid commands.");
		}
	}
}

//helpers
function travel(destination) {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'https://v-os.ca/assets/public/cli.php?a=travel&b=' + destination);
	xhr.onload = function() {
		if (xhr.status === 200) {
			console.log(xhr.responseText);
			if (xhr.responseText != "fail") {
				var destinationURL = "https://v-os.ca/" + xhr.responseText;
				window.location.href = destinationURL;
			} else outputText(travelFail);
		} else outputText(travelFail);
	};
	xhr.send();
}

function index() {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'https://v-os.ca/assets/public/cli.php?a=index');
	xhr.onload = function() {
		if (xhr.status === 200) outputText(xhr.responseText);
		else outputText(indexFail);
	};
	xhr.send();
}

function replaceUsername(string) {
	return string.replace("#[user]", username);
}

function outputText(string) {
	outputBox.innerHTML = string;
}

function getRandomInt(min, max) {
	return Math.floor((Math.random() * max) + min);
}

function getLocation() {
	var url = window.location.pathname;
	if (url == '/') url = '/home'; 
	loc = url.substring(1, url.length);
}

function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

function encodeHTML(s) {
	return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
}