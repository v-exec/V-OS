//===================================================== Variables

var cli = document.getElementById('cli');
var isDisplayed;

var outputBox = document.getElementById('lo-cli-output');
var inputBox = document.getElementById('lo-cli-input');

var username;
var loc;
var input;

//===================================================== Text

var noNameGreetings = [
"Hi, I'm LOGO. I can't seem to find you in my logs, what's your name?",
"Hello, my name's LOGO. I can't seem find you in my records, what would you like me to call you?",
"Welcome traveller, I'm LOGO. What's your name?"
];

var nameGreetings = [
"Hi #[user], welcome back to V-OS. If there's anything I can do, just write 'help' for a list of commands.",
"Welcome back, #[user]. Write 'help' if you need any assistance.",
"Hello #[user], your return is welcome."
];

var introductions = [
"Nice to meet you, #[user]. I am V-OS' guide. If there's anything you need help with, just write 'help'.",
"Welcome, #[user]. I am the guide for this construct world. If you need help, just write 'help'."
];

var rename = "Okay, I'll call you #[user] from now on.";

var commands = [
"<i><b>help</b></i> - get list of commands<br><br>",
"<i><b>logs</b></i> - get additional log information for page<br><br>",
"<i><b>index</b></i> - get list of navigation pages<br><br>",
"<i><b>travel page_name</b></i> - travel to page <i>page_name</i><br><br>",
"<i><b>rename new_name</b></i> - change name to <i>new_name</i><br><br>",
"<i><b>exit</b></i> - exit cli<br><br>",
"Alternatively, you may also write the first letter of each command as shorthand."
];

var help = commands[0];
for (var i = 1; i < commands.length; i++) help += commands[i];

var logsFail = "There seemed to be an error fetching this page's logs, my apologies.";
var indexFail = "There seemed to be an error fetching the indices, my apologies.";

//===================================================== Runtime

cli.style.display = "none";
intro();

//===================================================== Primary Functions

function toggleCLI() {
	if (isDisplayed) {
		cli.style.display = "none";
		isDisplayed = false;
		document.cookie = "isDisplayed=false; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
	}
	else {
		cli.style.display = "block";
		inputBox.focus();
		isDisplayed = true;
		document.cookie = "isDisplayed=true; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
	}
}

document.addEventListener("keydown", cliToggleCheck, false);

function cliToggleCheck(e) {
	if (e.keyCode == 13 && !isDisplayed) {
		e.preventDefault();
		cli.style.display = "block";
		inputBox.focus();
		isDisplayed = true;
		document.cookie = "isDisplayed=true; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
	}
}

inputBox.onpaste = function(e) {
	e.preventDefault();
}

function intro() {
	username = getCookie("username");
	isDisplayed = getCookie("isDisplayed");

	if (username == "") username = null;

	if (username != null) outputText(replaceUsername(nameGreetings[getRandomInt(0, nameGreetings.length)]));
	else outputText(noNameGreetings[getRandomInt(0, noNameGreetings.length)]);

	if (isDisplayed == "true") {
		cli.style.display = "block";
		inputBox.focus();
	} else {
		cli.style.display = "none";
		isDisplayed = false;
	}
}

function guide(e) {
	if (e.keyCode == 13) {
		e.preventDefault();
		input = inputBox.value;
		inputBox.value = null;

		if (username == null && input.length > 0) {
			username = input;
			document.cookie = "username=" + username +"; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
			outputText(replaceUsername(introductions[getRandomInt(0, introductions.length)]));

		} else if (input.toLowerCase().startsWith("help")) {
			outputText(help);

		} else if (input.toLowerCase().startsWith("h") && input.trim().length == 1) {
		outputText(help);

		} else if (input.toLowerCase().startsWith("rename")) {
			var newName = input.substring(6, input.length).trim();
			if (newName == "") outputText("I need you to write the new name you want me to call you.");
			else {
				username = newName;
				document.cookie = "username=" + username +"; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
				outputText(replaceUsername(rename));
			}

		} else if (input.toLowerCase().startsWith("r ")) {
			var newName = input.substring(2, input.length).trim();
			if (newName == "") outputText("I need you to write the new name you want me to call you.");
			else {
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

		} else if (input.toLowerCase().startsWith("logs")) {
			logs();

		} else if (input.toLowerCase().startsWith("l") && input.trim().length == 1) {
			logs();

		} else if (input.toLowerCase().startsWith("exit")) {
			toggleCLI();

		} else if (input.toLowerCase().startsWith("e") && input.trim().length == 1) {
			toggleCLI();

		} else {
			outputText("Write 'help' for a list of valid commands.");
		}
	}
}

//===================================================== Helpers

function travel(destination) {
	var destinationURL = "https://v-os.ca/" + destination;
	window.location.href = destinationURL;
}

function index() {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'https://v-os.ca/assets/public/cli.php?a=index');
	xhr.onload = function() {
		if (xhr.status === 200) {
			outputText(xhr.responseText);
		}
		else {
			outputText(indexFail);
		}
	};
	xhr.send();
}

function logs() {
	getLocation();

	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'https://v-os.ca/assets/public/cli.php?a=logs&l='+loc);
	xhr.onload = function() {
		if (xhr.status === 200) {
			outputText(xhr.responseText);
		}
		else {
			outputText(logsFail);
		}
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