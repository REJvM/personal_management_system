window.onload = function() {
    imageLoadFix();
};

function imageLoadFix() {
    let body = document.body;

    // Preload the background image
    let image = new Image();
    image.src = body.getAttribute('data-bg-image');;

    // Once the image is loaded, show the content
    image.onload = function() {
        body.style.visibility = 'visible';
        document.querySelector('section').style.display = 'block';
    
        focusOnLoad();
    };
}

function focusOnLoad() {
    let emailInput = document.querySelector('input[name="_username"]'); 

    if(emailInput.value === '') {
        setTimeout(function(){
            emailInput.focus();
        }, 700);        
    } else {
        let passwordInput = document.querySelector('input[name="_password"]'); 
        setTimeout(function(){
            passwordInput.focus();
        }, 700);
    }
    
};