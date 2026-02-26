let timer = null;
let seconds = 0;
let startTime = null;

function startTimer() {

    if (timer !== null) return; // prevent double start

    seconds = 0;
    document.getElementById("timer").innerText = "00:00:00";

    startTime = new Date();

    timer = setInterval(() => {
        seconds++;
        document.getElementById("timer").innerText = formatTime(seconds);
    }, 1000);
}

function stopTimer() {

    if (timer === null) return; // prevent stopping if not running

    clearInterval(timer);
    timer = null;

    let endTime = new Date();

    // Here you can send startTime and endTime to backend later
    console.log("Start:", startTime);
    console.log("End:", endTime);

    // Reset timer
    seconds = 0;
    document.getElementById("timer").innerText = "00:00:00";
}

function formatTime(sec) {
    let hrs = Math.floor(sec / 3600);
    let mins = Math.floor((sec % 3600) / 60);
    let secs = sec % 60;

    return (
        String(hrs).padStart(2, '0') + ":" +
        String(mins).padStart(2, '0') + ":" +
        String(secs).padStart(2, '0')
    );
}

function toggleTheme() {
    document.body.classList.toggle("dark");
}