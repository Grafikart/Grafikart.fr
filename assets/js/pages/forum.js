import {h, render} from 'preact'
import {Report} from '../components/Forum/Report'
import {$$} from '@fn/dom'
import {slideDown, slideUp} from '../modules/animation'

document.addEventListener('turbolinks:load', function () {

  $$('.js-report').forEach(report => {
    let form = null
    report.addEventListener('click', async function (e) {
      e.preventDefault()
      if (form === null) {
        const div = document.createElement('div')
        report.parentElement.insertAdjacentElement('afterend', div)
        render(
          h(Report, {
            endpoint: report.dataset.endpoint,
            data: JSON.parse(report.dataset.data)
          }),
          div
        )
        div.querySelector('textarea, input').focus()
        slideDown(div)
        form = div
      } else {
        await slideUp(form)
        form.remove()
        form = null
      }
    })
  })

})
