// Settings
let propulsion = 10;
let gravity = 2;
let fuelBurn = 0.5;
let offset = 0;
let coinFuel = 10;
let coinSpawn = 200;

// Temp?
const defaultPropulsion = 10;

// Utility
const modal = document.querySelector(".modal");
const buzz = document.querySelector("#buzz");
const buzzFlame = document.querySelector("#buzz-flame");
const buzzCoins = document.querySelector("#buzz-coins");
const indFuel = document.querySelector("#ind-fuel");
const indScore = document.querySelector("#ind-score");
const indPoints = document.querySelector("#ind-points");
const w = 800;
const h = 600;
const buzzW = buzz.offsetWidth;
const buzzH = buzz.offsetHeight;
const buzzOffset = 40;
const coinW = 32;

// Sound
const soundtrack = new Audio(
	"https://assets.codepen.io/5356857/soundtrack.mp3"
);
soundtrack.loop = true;
const fuelUp = new Audio("https://assets.codepen.io/5356857/energy-up.mp3");
const fuelDown = new Audio("https://assets.codepen.io/5356857/energy-down.mp3");

// Variables
let coins = [];
let buzzY = -500;
let buzzX = 0;
let fuel = 100;
let level = 0;
let counter = 0;
let running = false;
const triggers = {
	ArrowUp: false,
	ArrowRight: false,
	ArrowDown: false,
	ArrowLeft: false
};

const jetpack = (e) => {
	let key = e.key;

	// WASD Support
	switch (key) {
		case "w":
			key = "ArrowUp";
			break;
		case "s":
			key = "ArrowDown";
			break;
		case "a":
			key = "ArrowLeft";
			break;
		case "d":
			key = "ArrowRight";
			break;
	}
	triggers[key] = e.type === "keydown" ? true : false;
};
document.addEventListener("keydown", jetpack, false);
document.addEventListener("keyup", jetpack, false);

// Mobile Support
document.addEventListener("touchstart", function () {
	jetpack({
		key: "ArrowUp",
		type: "keydown"
	});
});
document.addEventListener("touchend", function () {
	jetpack({
		key: "ArrowUp",
		type: "keyup"
	});
});

// Mods - more mods coming soon
const coinType = [
	"bonus",
	"bonus",
	"bonus",
	"bonus",
	"malus",
	"speedup",
	"speeddown",
	"laser",
	//"morebonus",
	//"moremalus",
	//"buzzshrink",
	//"buzzgrow",
	//"buzzweapon",
];
const coinTypeN = coinType.length;

// New Coins
const generate = (n) => {
	for (let i = 0; i < (n | 1); i++) {
		coins.push({
			x: w + coinW + coinSpawn * (i + 1),
			y: Math.random() * (-h + coinW),
			t: coinType[Math.floor(Math.random() * coinTypeN)]
		});
	}
};

const restart = () => {
	buzzY = -500;
	buzzX = 0;
	level = 0;
	counter = 0;
	fuel = 100;
	coins = [];
	generate(3);
	running = true;
	modal.classList.add("deactive");
	soundtrack.currentTime = 0;
	soundtrack.play();
};

document.querySelector("#start").addEventListener("click", restart, false);

const movement = () => {
	if (running) {
		let accY = 1;

		if (triggers.ArrowUp && fuel > 0 && buzzY > -h + buzzOffset) {
			buzzY -= propulsion;
			fuel -= fuelBurn;
			accY = 2;
		}

		if (buzzY < buzzH - buzzOffset - offset) {
			let accX = 1;

			if (triggers.ArrowDown) {
				buzzY += propulsion;
				accY = 0.5;
			}
			if (triggers.ArrowRight && buzzX < w - buzzOffset) {
				buzzX += propulsion;
				accX = 2;
			}
			if (triggers.ArrowLeft && buzzX > -buzzW + buzzOffset) {
				buzzX -= propulsion;
				accX = 0.5;
			}

			buzzY += gravity;

			// Progress
			level += propulsion * 2 * accX;

			// Hitbox
			let indCoins = "";
			coins.forEach((coin, i) => {
				coin.x -= propulsion * 0.5 * accX;

				if (
					buzzY - buzzH + buzzOffset < coin.y &&
					buzzY - buzzOffset > coin.y &&
					buzzX + buzzOffset < coin.x &&
					buzzX + buzzW - buzzOffset > coin.x
				) {
					switch (coin.t) {
						case "malus":
							fuelDown.currentTime = 0;
							fuelDown.play();
							fuel -= coinFuel;
							buzz.style.filter = "invert(1)";
							setTimeout(function () {
								buzz.style.filter = "";
							}, 300);
							break;
						case "speedup":
							propulsion = defaultPropulsion * 2;
							soundtrack.playbackRate = 1.2;
							buzz.style.filter = "saturate(3)";
							setTimeout(function () {
								buzz.style.filter = "";
								propulsion = defaultPropulsion;
								soundtrack.playbackRate = 1;
							}, 3000);
							break;
						case "speeddown":
							propulsion = defaultPropulsion * 0.5;
							soundtrack.playbackRate = 0.5;
							buzz.style.filter = "grayscale(1)";
							setTimeout(function () {
								buzz.style.filter = "";
								propulsion = defaultPropulsion;
								soundtrack.playbackRate = 1;
							}, 3000);
							break;
						case "laser":
							//soundtrack.playbackRate = 0.5;
							document.querySelector('#container').style.setProperty("--laser", 50);
							offset = 200;
							setTimeout(function () {
								document.querySelector('#container').style.setProperty("--laser", 1);
								offset = 0;
								//buzz.style.filter = "";
								//propulsion = defaultPropulsion;
								//soundtrack.playbackRate = 1;
							}, 3000);
							break;
						default:
							fuelUp.currentTime = 0;
							fuelUp.play();
							fuel += coinFuel + Math.floor(Math.random() * coinFuel);
							counter++;
							buzz.style.filter = "brightness(1.5)";
							setTimeout(function () {
								buzz.style.filter = "";
							}, 300);
					}

					coins.splice(i, 1);
					generate();
				}

				if (coin.x < -coinW) {
					coins.splice(i, 1);
					generate();
				}

				indCoins +=
					'<span class="coin ' +
					coin.t +
					'" style="--x: ' +
					coin.x +
					"; --y: " +
					coin.y +
					//"; --f: " +
					//coin.f +
					';"></span>';
			});

			// Animations
			document.querySelector('#container').style.setProperty("--level", level);
			buzz.style.transform = "translate(" + buzzX + "px, " + buzzY + "px)";
			buzzFlame.style.setProperty("--buzzFlame", accY);
			buzzCoins.innerHTML = indCoins;
			indFuel.innerHTML = "🔋 " + fuel + "%";
			indScore.innerHTML = "🚀 " + (level + counter * 10000);
			indPoints.innerHTML = "🟢 " + counter;
		} else {
			console.log("Game Over");
			running = false;
			modal.classList.remove("deactive");
			soundtrack.pause();
		}
	}

	requestAnimationFrame(movement);
};
movement();
