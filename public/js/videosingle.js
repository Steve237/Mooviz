const video = document.querySelectorAll('.video');
//play pause forward backward 
const playBtn = document.querySelectorAll('.play-btn');
const backwardBtn = document.querySelectorAll('.back-btn');
const forwardBtn = document.querySelectorAll('.next-btn');
let isVideoPlaying = true;

video.forEach((item,index)=>{
  video[index].autoplay=true;
  video[index].autobuffer=true;
  video[index].loop=true;
  video[index].volume=1;
  
  playBtn[index].addEventListener('click',function(){
    isVideoPlaying?pauseVideo(index):playVideo(index);
  });
  
});
//play video
function playVideo(index){
  isVideoPlaying = true;
  video[index].play();
  playBtn[index].classList.replace('fa-play-circle-o','fa-pause-circle-o')
}
//pause video
function pauseVideo(index){
  isVideoPlaying = false;
  video[index].pause();
  playBtn[index].classList.replace('fa-pause-circle-o','fa-play-circle-o')
}
//forward video
forwardBtn[0].addEventListener('click',function(){
  video[0].currentTime+=10;
});


//backward video
backwardBtn[0].addEventListener('click',function(){
  video[0].currentTime-=10;
});

//timebar
const timebarCurrent = document.querySelectorAll('.video-timebar-current');
const timebarBuffer = document.querySelectorAll('.video-timebar-buffered');
const timebarCircle = document.querySelectorAll('.video-timebar-circle');
const timebarToolkit = document.querySelectorAll('.video-timebar-toolkit');

video[0].addEventListener('timeupdate',(event)=>{
  timebarCurrent[0].style.width=video[0].currentTime/video[0].duration*100+"%";
  timebarBuffer[0].style.width=video[0].buffered.length/video[0].duration*100+"%";
  timebarCircle[0].style.left=video[0].currentTime/video[0].duration*100+"%";
  timebarToolkit[0].style.left=video[0].currentTime/video[0].duration*100+"%";
});

//current time and duration time
const videoCurrentTimeBox = document.querySelectorAll('.video-current-time-box');
const videoDurationTimeBox = document.querySelectorAll('.video-duration-time-box');

let videoPlaying = setInterval(function(){
  //current time box
  let videoCurrentTimeSec = Math.floor(video[0].currentTime%60);
  if(videoCurrentTimeSec<=9){
    videoCurrentTimeSec="0"+videoCurrentTimeSec;
  }
  else{
    videoCurrentTimeSec=videoCurrentTimeSec;
  }
  let videoCurrentTimeMin = Math.floor(video[0].currentTime/60%60);
  if(videoCurrentTimeMin<=9){
    videoCurrentTimeMin="0"+videoCurrentTimeMin;
  }
  else{
    videoCurrentTimeMin=videoCurrentTimeMin;
  }
  let videoCurrentTimeHour = Math.floor(video[0].currentTime/60/60%60);
  if(videoCurrentTimeHour<=9){
    videoCurrentTimeHour="0"+videoCurrentTimeHour;
  }
  else{
    videoCurrentTimeHour=videoCurrentTimeHour;
  }
  
  
  videoCurrentTimeBox[0].innerText = videoCurrentTimeHour+":"+videoCurrentTimeMin+":"+videoCurrentTimeSec;
  
  //duration time box
  let videoDurationTimeSec = Math.floor(video[0].duration%60);
  if(videoDurationTimeSec<=9){
    videoDurationTimeSec="0"+videoDurationTimeSec;
  }
  let videoDurationTimeMin = Math.floor(video[0].duration/60%60);
  if(videoDurationTimeMin<=9){
    videoDurationTimeMin="0"+videoDurationTimeMin;
  }
  let videoDurationTimeHour = Math.floor(video[0].duration/60/60%60);
  if(videoDurationTimeHour<=9){
    videoDurationTimeHour="0"+videoDurationTimeHour;
  }
  
  
  videoDurationTimeBox[0].innerText = videoDurationTimeHour+":"+videoDurationTimeMin+":"+videoDurationTimeSec;
  timebarToolkit[0].innerText=videoCurrentTimeBox[0].innerText;
  
},100);


//timebar click
const videoTimebar = document.querySelectorAll('.video-timebar');

videoTimebar[0].addEventListener('click',(evnt)=>{
  video[0].currentTime=(evnt.offsetX/evnt.srcElement.clientWidth)*video[0].duration;
});
//mute and volume btn
const volumeBtn = document.querySelectorAll('.video-sound-control-icon');
const volumeBtnIcon = document.querySelectorAll('.volume-btn');
let isVideoMute = false;

volumeBtn[0].addEventListener('click',function(){
  isVideoMute?unmuteVideo():muteVideo();
});

function muteVideo(){
  isVideoMute = true;
  video[0].volume=0;
  volumeBtnIcon[0].classList.replace('fa-volume-up','fa-volume-off');
  volumeControlCurrentMeter[0].style.width=0+"%";
}

function unmuteVideo(){
  isVideoMute = false;
  video[0].volume=1;
  volumeBtnIcon[0].classList.replace('fa-volume-off','fa-volume-up');
  volumeControlCurrentMeter[0].style.width=100+"%";
}
//volume control
const volumeControlCurrentMeter = document.querySelectorAll('.video-sound-control-meter-current');
const volumeControlMeter = document.querySelectorAll('.video-sound-control-meter');

volumeControlMeter[0].addEventListener('click',function(evnt){
  video[0].volume=evnt.offsetX/evnt.srcElement.clientWidth;
  let temp = evnt.offsetX/evnt.srcElement.clientWidth;
  volumeControlCurrentMeter[0].style.width=(temp)*100+"%";
  //isVideoMute?unmuteVideo():muteVideo();
  
});

//fullsceen mode
const fullsceenIcon = document.querySelectorAll('.video-full-screen-icon');
fullsceenIcon[0].addEventListener('click',function(){
      
if (video[0].requestFullscreen) {
video[0].requestFullscreen();
} else if (video[0].webkitRequestFullscreen) { /* Safari */
video[0].webkitRequestFullscreen();
} else if (video[0].msRequestFullscreen) { /* IE11 */
video[0].msRequestFullscreen();
}
      
});
