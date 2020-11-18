function onClickAddPlaylist(event) {

    event.preventDefault();

  const url = this.href;

  const symbole = this.querySelector('i');

  axios.get(url).then(function(response) {

    if(symbole.classList.contains('fas')) { 

      symbole.classList.replace('fas', 'far');

    } else {

      symbole.classList.replace('far', 'fas');
    }
    
  });

  }

  document.querySelectorAll('a.js-play').forEach(function(link){

    link.addEventListener('click', onClickAddPlaylist);
})