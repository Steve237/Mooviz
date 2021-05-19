
//permet de rendre visible la barre de progrès lors du téléchargement
function hideBar() {

    document.getElementById("progress-bar").className = "visible";
}

//permet d'afficher le progrès de l'upload via la barre de progrès
$(document).ready(function () {
    // File upload via Ajax
    $("#background_video_update").on('submit', function (e) {
        e.preventDefault();
        var video_update = $(this);
        var post_url = video_update.attr('action');
        var post_data = video_update.serialize();
        $.ajax({
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        $(".progress-bar").width(percentComplete + '%');
                        $(".progress-bar").html(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            type: 'POST',
            url: post_url,
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $(".progress-bar").width('0%');
            },
            error: function () {
                $('#uploadStatus').html(
                    '<p class="upload-video-failed">Le téléchargement a échoué, merci de recommencer</p>'
                );
            },
            success: function (resp) {
                if (resp == 'ok') {
                    window.document.location = '/admin/success_update_video_background';
                } else if (resp == 'err') {
                    $('#uploadStatus').html('<p class="upload-video-failed">Nous autorisons uniquement les vidéos au format mp4.</p>');
                }
            }
        });
    });
});