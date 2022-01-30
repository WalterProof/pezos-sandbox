import { Controller } from '@hotwired/stimulus';
import Choices from 'choices.js';

import { addClass, removeClass } from '../util/class';

export default class extends Controller {
    static targets = ['holder', 'form'];

    connect() {
        new Choices('#token-choices', {
            shouldSort: false,
            fuseOptions: {
                distance: 400,
            },
        });
        removeClass(this.formTarget, 'hidden');
        addClass(this.holderTarget, 'hidden');
    }
}
