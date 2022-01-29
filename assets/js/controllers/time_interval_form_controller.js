import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form'];

    connect() {
        const radios = this.formTarget.elements['interval'];
        radios.forEach((radio) =>
            radio.addEventListener('change', () => this.formTarget.submit())
        );
    }
}
