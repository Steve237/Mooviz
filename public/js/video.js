const player = new Plyr("#player", {captions: {active: true},


ratio:'16:9',
autoplay: true,

controls: [ 
    'play', 
    'progress', 
    'current-time', 
    'mute', 
    'volume', 
    'captions', 
    'settings', 
    'pip', 
    'airplay', 
    'fullscreen'
]

});

// Expose player so it can be used from the console
window.player = player;
