(function() {
    var settings, threshold;
    var resize_interval;

    document.addEventListener("DOMContentLoaded", function() {
        processSettings();
        lazyLoadImages();
    });

    window.addEventListener('resize', function() {
        clearTimeout(resize_interval);
        resize_interval = setTimeout(lazyLoadImages, 750);
    });

    function processSettings() {
        settings = window.PLL_load_settings;

        if (settings.image_size_threshold) {
            threshold = settings.image_size_threshold / 100 + 1;
        } else {
            threshold = 1;
        }
    }

    function lazyLoadImages() {
        var images = document.getElementsByClassName(settings.lazy_loading_class);

        for (var i = 0; i < images.length; i++) {
            var current_image = images[i];
            var data_src = current_image.getAttribute('data-src');

            if (data_src && data_src.charAt(0) === '{') {
                var sizes = JSON.parse(data_src);
                var width = current_image.offsetWidth;

                for (var size in sizes) {
                    if (!sizes.hasOwnProperty(size)) {
                        continue;
                    }
                    var size_data = sizes[size];
                    if (size_data.width >= width / threshold) {
                        current_image.setAttribute('src', size_data.src);
                        break;
                    }
                }
            } else if (data_src) {
                current_image.setAttribute('src', data_src);
                current_image.removeAttribute('data-src');
            }
        }
    }
})();