function stopVideo() {

    var video = document.getElementById('video-header');
    video.currentTime = 0;
    video.pause();
}

function videoStop() {

    var video = document.getElementById('video-background');
    video.currentTime = 0;
    video.pause();
}