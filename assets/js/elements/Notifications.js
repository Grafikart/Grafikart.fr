import {Notifications} from '../components/Notifications/Notifications'
import {h, render} from 'preact'

const url = new URL('http://grafikart.localhost:8001/.well-known/mercure');
url.searchParams.append('topic', 'http://grafikart.fr/notifications/{channel}');

const eventSource = new EventSource(url, {
  withCredentials: true
});

eventSource.onmessage = e => console.log(e); // do something with the payload

class NotificationsElement extends HTMLElement {

    connectedCallback () {
      this.innerHTML = 'Notifications'
      render(h(Notifications), this)
      eventSource.onmessage = e => {
        console.log(e)
        const data = JSON.parse(e.data)
        this.innerHTML += `<div class="formatted">
      <strong>${data.message}</strong> - ${data.url}<br>
        Juste à l'instant
      </div>`
      }

    }

}

customElements.define('site-notifications', NotificationsElement)
