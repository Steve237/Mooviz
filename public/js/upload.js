//permet de rendre visible la barre de progrès lors du téléchargement
function changeClass() {

    document.getElementById("progress-bar").className = "visible";


}

//permet d'afficher le progrès de l'upload via la barre de progrès
$(document).ready(function(){
    // File upload via Ajax
    $("#uploadForm").on('submit', function(e){
        e.preventDefault();
        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        $(".progress-bar").width(percentComplete + '%');
                        $(".progress-bar").html(percentComplete+'%');
                    }
                }, false);
                return xhr;
            },
            type: 'POST',
            url: '/upload',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                $(".progress-bar").width('0%');
            },
            error:function(){
                $('#uploadStatus').html('<p style="color:#EA4335;">Le téléchargement a échoué, merci de recommencer.</p>');
            },
            success: function(resp){
                if(resp == 'ok'){
                    window.document.location = '/upload_video_successfull';
                }else if(resp == 'err'){
                    $('#uploadStatus').html('<p style="color:#EA4335;">Le téléchargement a échoué: nous acceptons uniquement les vidéos au format mp4 et les images au format jpg, jpeg, ou png</p>');
                }
            }
        });
    });
	
    // File type validation
    $("#fileInput").change(function(){
        var allowedTypes = ['video/mp4'];
        var file = this.files[0];
        var fileType = file.type;
        if(!allowedTypes.includes(fileType)){
            alert('Veuillez télécharger une vidéo au format mp4.');
            $("#fileInput").val('');
            return false;
        }
    });
});