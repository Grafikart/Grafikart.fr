import {h, render} from 'preact'
import {Report} from '../components/Forum/Report'
import {$$} from '@fn/dom'

document.addEventListener('turbolinks:load', function () {

  $$('.js-report').forEach(report => {
    // report.addEventListener('click', function (e) {
    //  e.preventDefault()
      const div = document.createElement('div')
      report.parentElement.insertAdjacentElement('afterend', div)
      render(
        h(Report),
        div
      )
    // })
  })

})
