import '../css/app.scss'


document.querySelector('.header__account').addEventListener('click', function (e) {
    e.preventDefault()
    document.body.classList.toggle('dark-mode')
})
