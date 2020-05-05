import {h, render} from 'preact'
import {Report} from '../components/Forum/Report'
import {$$} from '@fn/dom'

document.addEventListener('turbolinks:load', function () {

  $$('.js-report').forEach(report => {
      render(
        h(Report, {
          endpoint: report.dataset.endpoint,
          data: JSON.parse(report.dataset.data)
        }),
        report
      )
  })

})
