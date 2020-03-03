import Button from './Button'
import {strToDom} from '@fn/dom'

/**
 * @property {boolean} listening
 * @property {webkitSpeechRecognition} recognition
 */
export default class SpeechButton extends Button {

  constructor (editor) {
    super(editor)
    this.listening = false
    if (window.hasOwnProperty('webkitSpeechRecognition')) {
      this.recognition = new webkitSpeechRecognition()
      this.recognition.lang = 'fr-FR'
      this.recognition.continuous = true
      this.recognition.interimResults = false
    }
  }

  icon () {
    if (!window.hasOwnProperty('webkitSpeechRecognition')) {
      return null
    }
    this.icon = strToDom(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M96 256V96c0-53.019 42.981-96 96-96s96 42.981 96 96v160c0 53.019-42.981 96-96 96s-96-42.981-96-96zm252-56h-24c-6.627 0-12 5.373-12 12v42.68c0 66.217-53.082 120.938-119.298 121.318C126.213 376.38 72 322.402 72 256v-44c0-6.627-5.373-12-12-12H36c-6.627 0-12 5.373-12 12v44c0 84.488 62.693 154.597 144 166.278V468h-68c-6.627 0-12 5.373-12 12v20c0 6.627 5.373 12 12 12h184c6.627 0 12-5.373 12-12v-20c0-6.627-5.373-12-12-12h-68v-45.722c81.307-11.681 144-81.79 144-166.278v-44c0-6.627-5.373-12-12-12z"/></svg>`)
    return this.icon
  }

  /**
   * @param {Editor} editor
   */
  action () {
    if (this.listening === true) {
      this.recognition.stop()
      this.listening = false
      this.icon.style.fill = null
      return
    }
    this.icon.style.fill = 'red'
    this.recognition.start()
    this.listening = true
    this.recognition.onresult = (e) => {
      let result = e.results.item(e.resultIndex)
      if (result.isFinal === true) {
        const transcript = result.item(0).transcript
        const sentence = transcript.charAt(0).toUpperCase() + transcript.slice(1)
        this.editor.replace(sentence)
      }
    }

  }

}
