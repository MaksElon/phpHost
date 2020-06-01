$(function() {
    let dialogCropper = $("#modal");
    $("#customFile").on("change", function() {
        if (this.files && this.files.length) {
            let file = this.files[0];
            let reader = new FileReader();
            reader.onload = function(e) {
                dialogCropper.modal('show');
                cropper.replace(e.target.result);
            }
            reader.readAsDataURL(file);

        }
    });

    const image = document.getElementById('cropperImg');
    let lastValidCrop;
    const cropper = new Cropper(image, {
        aspectRatio: 1/1,
        viewMode: 1,
        autoCropArea: 0.5,
        crop(e) {
            let validCrop = true;
            if (e.detail.width < 300) validCrop = false;
            if (e.detail.height < 300) validCrop = false;

            if (validCrop) {
                lastValidCrop = cropper.getData();
                $("#crop_photo_x").val(e.detail.x);
                $("#crop_photo_y").val(e.detail.y);
                $("#crop_photo_width").val(e.detail.width);
                $("#crop_photo_height").val(e.detail.height);
            } else {
                cropper.setData(lastValidCrop);
            }
        },
    });

    $("#img-rotation").on("click",function (e) {
        e.preventDefault();
        cropper.rotate(45);
    });

    $("#saveImg").on("click", function (e) {
        e.preventDefault();

        let imgContent = cropper.getCroppedCanvas().toDataURL();
        $("#hide").val(imgContent);
        $("#output").attr("src", imgContent);
        dialogCropper.modal('hide');
    });
});