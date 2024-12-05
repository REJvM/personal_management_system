window.onload = function() {
    let body = document.body;

    // Preload the background image
    let image = new Image();
    image.src = body.getAttribute('data-bg-image');;

    // Once the image is loaded, show the content
    image.onload = function() {
        body.style.visibility = 'visible';
        document.querySelector('section').style.display = 'block';
    };
};