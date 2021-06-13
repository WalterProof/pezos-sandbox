import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['metadata'];

    connect() {
        this.prettyPrint();
        this.metadataTarget.addEventListener('change', () =>
            this.prettyPrint()
        );
    }

    prettyPrint() {
        var obj = JSON.parse(this.metadataTarget.value);
        var pretty = JSON.stringify(obj, undefined, 4);
        this.metadataTarget.value = pretty;
    }
}
