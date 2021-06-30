import { Controller } from 'stimulus';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['metadata'];

    connect() {
        this.prettyPrint();
        this.metadataTarget.addEventListener('change', () =>
            this.prettyPrint()
        );
    }

    prettyPrint() {
        const obj = JSON.parse(this.metadataTarget.value);
        const pretty = JSON.stringify(obj, undefined, 4);
        this.metadataTarget.value = pretty;
    }

    addCollectionItem(e) {
        const $collectionHolderClass = $(e.currentTarget).data(
            'collectionHolderClass'
        );
        this.addFormToCollection($collectionHolderClass);
    }

    addFormToCollection($collectionHolderClass) {
        const $collectionHolder = $('.' + $collectionHolderClass);
        const prototype = $collectionHolder.data('prototype');
        const index = $collectionHolder.data('index');

        let newForm = prototype;
        newForm = newForm.replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        const $newFormBlock = $('<div></div>').append(newForm);
        this.addFormDeleteLink($newFormBlock);
        $collectionHolder.append($newFormBlock);
    }

    addFormDeleteLink($formBlock) {
        const $removeFormButton = $(
            '<button class="btn btn-danger float-right">Delete</button>'
        );
        $formBlock.prepend($removeFormButton);

        $removeFormButton.on('click', function () {
            $formBlock.remove();
        });
    }

    deleteCollectionItem(e) {
        $(e.currentTarget).parent('div').remove();
    }
}
