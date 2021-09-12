import { Controller } from 'stimulus';
import Choices from 'choices.js';

import { addClass, removeClass } from '../util/class';

export default class extends Controller {
    static targets = ['holder', 'form'];

    connect() {
        new Choices('#token-choices', {
            shouldSort: false,
        });
        removeClass(this.formTarget, 'hidden');
        addClass(this.holderTarget, 'hidden');
    }
}
