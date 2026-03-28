import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['container', 'button', 'buttonWrapper'];
    static values = { url: String };

    async load(event) {
        const button = event.currentTarget;
        const nextOffset = button.dataset.nextOffset;

        button.disabled = true;
        button.textContent = 'Loading...';

        const response = await fetch(`${this.urlValue}&offset=${nextOffset}`);
        const html = await response.text();

        const hasMore = response.headers.get('X-Has-More') === '1';
        const newNextOffset = response.headers.get('X-Next-Offset');

        this.containerTarget.insertAdjacentHTML('beforeend', html);

        if (hasMore) {
            button.dataset.nextOffset = newNextOffset;
            button.disabled = false;
            button.textContent = button.textContent.includes('Reviews') ? 'Load More Reviews' : 'Load More';
        } else {
            this.buttonWrapperTarget.remove();
        }
    }

    buttonTargetConnected(button) {
        button.addEventListener('click', this.load.bind(this));
    }
}
