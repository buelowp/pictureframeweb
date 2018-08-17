$(document).ready(function (e) {
    $("#uploadform").on('submit',(function(e) {
        e.preventDefault();
        $.ajax({
            url: "uploader.php",
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            beforeSend : function() {
                $("#preview").fadeOut();
                $("#err").fadeOut();
            },
            success: function(data) {
                if(data == 'invalid') {
                    // invalid file format.
                    $("#err").html("Invalid File !").fadeIn();
                }
                else {
                    // view uploaded file.
                    $("#err").html(data).fadeIn();
                    $("#uploadform")[0].reset();
                }
            },
            error: function(e) {
                $("#err").html(e).fadeIn();
            }
        });
    }));
});

function updateFilename()
{
    var fullPath = document.getElementById('uploadImage').value;
    var d = document.getElementById('preview');
    var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
    var filename = fullPath.substring(startIndex);
    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
        filename = filename.substring(1);
    }
    d.innerHTML = filename;
}
