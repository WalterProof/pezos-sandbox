import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form'];

    connect() {
      this.formTarget.querySelectorAll('input').forEach(item => {
        item.addEventListener('change', () => this.formTarget.submit())
      })
    }
}
