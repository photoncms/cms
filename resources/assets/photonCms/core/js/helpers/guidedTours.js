import { storage } from '_/services/storage';

import 'intro.js/introjs.css';

export const showIntro = {
    /**
    * Shows the generator guided tour
    *
    * @return  {void}  
    */
    generator (vueInstance) {
        const introViewed = storage.get('intro-generator', true);

        if (introViewed) {
            // return;
        }

        vueInstance.$intro()
            .exit()
            .setOptions({
                showBullets: false,
                exitOnOverlayClick: false,
                showStepNumbers: false,
                keyboardNavigation: true
            })
            .addSteps([
                {
                    intro: 'This is Photon CMS generator ⚙️. <br><br> You\'ll be using it to create and manage Photon CMS Modules. <br><br> <strong>Click the next button to learn how to create your first module!</strong>',
                },
            ])
            .start();

        storage.save('intro-generator', true, true);
    },

    /**
    * Shows the dashboard guided tour
    *
    * @return  {void}  
    */
    dashboard (vueInstance) {
        const introViewed = storage.get('intro-dashboard', true);

        if (introViewed) {
            // return;
        }

        vueInstance.$intro()
            .exit()
            .setOptions({
                showBullets: false,
                exitOnOverlayClick: false,
                showStepNumbers: false,
                keyboardNavigation: true
            })
            .addSteps([
                {
                    intro: 'Welcome to Photon CMS guided tour! 🎉<br><br>Click the Next button to continue exploring the dashboard.',
                },
                {
                    element: '#intro-dashboard-guided-tours',
                    intro: 'Tip: If you need to access this guided tour again you can do so from the \'Guided tours\' sidebar menu',
                    position: 'right',
                },
                {
                    element: '#intro-dashboard-user',
                    intro: 'This is the name of the logged in user. Clicking on a name will take you to the profile editing page.',
                    position: 'left',
                },
                {
                    element: '#intro-dashboard-quick-launch',
                    intro: 'Click the Add Shortcut button to add links to quick launch bar for the modules you use the most.',
                    position: 'bottom',
                },
                {
                    element: '#intro-dashboard-new-widget',
                    intro: 'Click the + sign to add a new dashboard widget. Dashboard widgets are great for displaying quick statistical data such as a list of latest registered users.',
                    position: 'right',
                },
                {
                    element: '#intro-dashboard-main-menu',
                    intro: 'The main menu gives you access to the modules you\'re about to create, asset manager and various system tools.',
                    position: 'right',
                },
                {
                    element: '#intro-dashboard-generator-link',
                    intro: 'Get started by creating your first Photon module by clicking on the Generator link.',
                    position: 'right',
                },
            ])
            .start();

        storage.save('intro-dashboard', true, true);
    },
};