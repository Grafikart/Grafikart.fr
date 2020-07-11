const ratio = 0.02
const options = {
  root: null,
  rootMargin: '0px',
  threshold: ratio
}
const handleIntersect = function (entries, observer) {
  entries.forEach(entry => {
    if (entry.intersectionRatio > ratio) {
      entry.target.classList.add('in')
      if (entry.target.dataset.delay) {
        entry.target.style.transitionDelay = `.${entry.target.dataset.delay}s`
      }
      observer.unobserve(entry.target)
    }
  })
}
const observer = new IntersectionObserver(handleIntersect, options)

document.addEventListener('turbolinks:load', () => {
  document.querySelectorAll('.fade').forEach(r => {
    observer.observe(r)
  })
})

document.addEventListener('turbolinks:before-render', () => {
  observer.takeRecords()
})
