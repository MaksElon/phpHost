<div>
<div class="modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <img id="cropperImg" src="images/default.jpg" height="400">
                </div>
            </div>
            <div class="modal-footer">
                <a id="img-rotation" class="btn btn-success text-light"><i class="fa fa-repeat" > 45°</i></a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Назад</button>
                <button id="saveImg" class="btn btn-primary">Зберегти фото</button>
            </div>
        </div>
    </div>
</div>
    <script>
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
    </script>
</div>
