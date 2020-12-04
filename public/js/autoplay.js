var vid = document.getElementsByName("uservideo");

[].forEach.call(vid, function (item) {
    item.addEventListener('mouseover', hoverVideo, false);
    item.addEventListener('mouseout', hideVideo, false);
});



function hoverVideo(e){
    this.play();
    
}
    
function hideVideo(e){
    
    this.pause();
    this.currentTime = 20;
}