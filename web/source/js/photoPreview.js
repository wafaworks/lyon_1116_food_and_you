$(document).ready(function () {
    var _config    = {
        prefix: "photoPreview"
    };
    var _methods = {};
    _methods.elementsToRender = function () {
        $("."+_config.prefix+":not(.active)").get().forEach(function (node) {
            _methods.render(node);
        });
    };

    _methods.render = function (node) {
        // mark
        var $node = $(node);

        $node.addClass("active");
        var input = $node.find("."+_config.prefix+"_control").find("input[type=\"file\"]").get();
        var wall  = $node.find("."+_config.prefix+"_wall");

        $(wall).on('click', function () {
            $(input).trigger("click");
        });
        $node.find("> input[type=\"button\"]").on('click', function () {
            $(input).trigger("focus");
            $(input).trigger("click");
        });

        $(input).on('change', function () {
            var file = this.files[0];
            if (this.files.length && file) {

                loadImage.parseMetaData(
                    file,
                    function (data) {
                        var degrees = 0;
                        if(data.hasOwnProperty('exif')){
                            var orientation = data.exif[0x0112];
                            switch(orientation) {
                                case 3:
                                    degrees = 180;
                                    break;
                                case 6:
                                    degrees = 90;
                                    break;
                                case 8:
                                    degrees = -90;
                                    break;
                                default:
                                    degrees = 0;
                            }
                        }

                        loadImage(
                            file,
                            function (canvas) {

                                var mycanvas = document.createElement("canvas");

                                if (degrees === 90 || degrees === -90) {
                                    mycanvas.width = canvas.height;
                                    mycanvas.height = canvas.width;
                                } else {
                                    mycanvas.width = canvas.width;
                                    mycanvas.height = canvas.height;
                                }


                                var ctx3 = mycanvas.getContext("2d");

                                if (degrees == 90) {
                                    ctx3.translate(canvas.height, 0);
                                }

                                if (degrees == -90) {
                                    ctx3.translate(0, canvas.width);
                                }

                                if (degrees == 180) {
                                    ctx3.translate(canvas.width, canvas.height);
                                }

                                ctx3.rotate((Math.PI/180)*degrees);
                                ctx3.drawImage(canvas,0,0);

                                $node.addClass("selected");
                                $(wall).css("backgroundImage", "url(\""+mycanvas.toDataURL()+"\")");
                            },
                            {
                                maxWidth: 600,
                                //maxHeight: 300,
                                orientation: true,
                                canvas: true
                            } // Options
                        );
                    }
                );




            } else {
                $node.removeClass("selected");
                $(wall).css("backgroundImage", "");
            }
        });
    };

    setInterval(function () {
        _methods.elementsToRender();
    }, 500);
});
