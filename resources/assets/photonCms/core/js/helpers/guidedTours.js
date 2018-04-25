import { storage } from '_/services/storage';

import { store } from '_/vuex/store';

import 'intro.js/introjs.css';

const introOptions = {
    showBullets: false,
    exitOnOverlayClick: false,
    showStepNumbers: false,
    keyboardNavigation: false,
    overlayOpacity: .25,
    scrollToElement: true,
};

export const showIntro = {
    /**
    * Shows the generator guided tour
    *
    * @return  {void}  
    */
    generator (vueInstance) {
        const introViewed = storage.get('intro-generator', true);

        if (introViewed) {
            return;
        }

        const intro = vueInstance.$intro()
            .exit()
            .setOptions(introOptions)
            .onbeforechange(function () {
                if (this._currentStep === 2) {
                    if(store.state.generator.selectedModule.name != 'Articles' 
                    && store.state.generator.selectedModule.table_name != 'articles') {
                        alert('Please, enter `Articles` as a Module Name and `articles` as a Table Name.');

                        $('#intro-generator-module').find('#name').focus();

                        return false;
                    }
                }

                if(this._currentStep === 3) {
                    if(!store.state.generator.selectedModule.fields.length > 0) {
                        alert('Please, click the Add new field button.');

                        return false;
                    }
                }

            })
            .onafterchange(function () {
                if (this._currentStep === 1) {
                    $('#intro-generator-module').find('#name').focus();
                }
            })
            .addSteps([
                {
                    intro: 'This is Photon CMS Generator. ⚙️ <br><br> You\'ll be using it to create and manage Photon CMS Modules. <br><br> <strong>Click the next button to learn how to create your first module!</strong>',
                    disableInteraction: true,
                },
                {
                    element: '#intro-generator-module',
                    intro: 'You only need to fill module name for now. Let\'s enter <strong>`Articles`</strong>.<br><br> Notice how table name is auto-generated for you.<br><br> <strong>Click next when you\'re done.</strong>',
                    position: 'right',
                },
                {
                    element: '#intro-generator-add-new-field',
                    intro: 'Next, click the Add New Field button to add the first field to our module called Articles.',
                    position: 'right',
                },
                {
                    intro: 'Great! Now all you need to do is enter a <strong>Field Name</strong> and hit the <strong>Create Module</strong> button and you\'re done.<br><br>The shortcut to your new module will appear in the main menu.<br><br>Happy coding! 😎',
                },
            ])
            .start();

        storage.save('intro-generator', true, true);

        return intro;
    },

    /**
    * Shows the dashboard guided tour
    *
    * @return  {void}  
    */
    dashboard (vueInstance) {
        const introViewed = storage.get('intro-dashboard', true);

        if (introViewed) {
            return;
        }

        vueInstance.$intro()
            .exit()
            .setOptions(introOptions)
            .addSteps([
                {
                    intro: 'Welcome to Photon CMS guided tour! 🎉<br><br>Click the Next button to continue exploring the dashboard.',
                    disableInteraction: true,
                },
                {
                    element: '#intro-dashboard-guided-tours',
                    intro: 'Tip: If you need to access this guided tour again you can do so from the \'Guided tours\' sidebar menu',
                    position: 'right',
                    disableInteraction: true,
                },
                {
                    element: '#intro-dashboard-user',
                    intro: 'This is the name of the logged in user. Clicking on a name will take you to the profile editing page.',
                    position: 'left',
                    disableInteraction: true,
                },
                {
                    element: '#intro-dashboard-quick-launch',
                    intro: 'Click the Add Shortcut button to add links to quick launch bar for the modules you use the most.',
                    position: 'bottom',
                    disableInteraction: true,
                },
                {
                    element: '#intro-dashboard-new-widget',
                    intro: 'Click the + sign to add a new dashboard widget. Dashboard widgets are great for displaying quick statistical data such as a list of latest registered users.',
                    position: 'right',
                    disableInteraction: true,
                },
                {
                    element: '#intro-dashboard-main-menu',
                    intro: 'The main menu gives you access to the modules you\'re about to create, asset manager and various system tools.',
                    position: 'right',
                    disableInteraction: true,
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