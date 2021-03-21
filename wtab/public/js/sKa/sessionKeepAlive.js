function sessionKeepAlive() {
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', APPLICATION_HOME);
    httpRequest.send(null);
}

setInterval(sessionKeepAlive, 100000);