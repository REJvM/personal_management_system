const mobileMenuButton = document.querySelector('button.mobile-menu');

mobileMenuButton.addEventListener('click', function() {
    var navigation = document.querySelector('nav');
    console.log(navigation.classList.contains('open'))
    if (navigation.classList.contains('open')) {
        navigation.classList.remove('open')
    } else {
        navigation.classList.add('open');
    }
})
