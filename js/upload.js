function readFile() {

    if (this.files && this.files[0]) {
        const size = 51;
        const file = new FileReader();

        file.onload = function(e) {
        	
        	        	
            const image = new Image();
            image.src = file.result;
            image.onload = function() {
                if (image.width <  size  && image.height < size ){
                    document.getElementById("res").src       =e.target.result
                    document.querySelector("#img").value = e.target.result;
                }else{

                    alert('max size 50 * 50 ')
                }
            };

        };


        file.readAsDataURL( this.files[0] );
    }

}

document.getElementById("img_upload").addEventListener("change", readFile);