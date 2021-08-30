import { startStimulusApp } from '@symfony/stimulus-bridge';
import { Dropdown, Tabs } from 'tailwindcss-stimulus-components';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(
    require.context(
        '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
        true,
        /\.(j|t)sx?$/
    )
);

// register any custom, 3rd party controllers here
app.register('dropdown', Dropdown);
app.register('tabs', Tabs);
