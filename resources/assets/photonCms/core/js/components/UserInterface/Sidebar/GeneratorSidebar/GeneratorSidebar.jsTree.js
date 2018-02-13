// Import store so that actions can be called
import { store } from '_/vuex/store';
// Import handler functions
import nodeSelectHandler from '_/components/UserInterface/Sidebar/GeneratorSidebar/GeneratorSidebar.nodeSelectHandler';

// Export jsTreeInstance object so it can be manipulated outside this module
export let jsTreeInstance;

// Sets up jsTree instance
export const setupJsTree = () => {
    $('#tree')
        .jstree({
            core: {
                dblclick_toggle: false,
                data: function(obj, jsTreeCallback) {
                    if (obj.id !== '#') {
                        return;
                    }

                    jsTreeCallback.call(this, store.state.generator.nodes);
                },
                multiple: false,
                themes: {
                    name: 'proton',
                    responsive: true,
                },
            }
        })
        .on('ready.jstree', onJsTreeReady)

        .on('refresh.jstree', onJsTreeReady)

        .on('changed.jstree', nodeSelectHandler);

    jsTreeInstance = $('#tree').jstree(true);

    return jsTreeInstance;
};

export const onJsTreeReady = () => {
    // In no module is being edited
    if (!store.state.generator.selectedModule) {
        jsTreeInstance.deselect_all(true);

        return;
    }

    // If module is being edited, first deselect any selected nodes
    jsTreeInstance.deselect_all(true);

    // Then select the node by id from currently edited module
    jsTreeInstance.select_node(store.state.generator.selectedModule.id);
};

export const refreshJsTree = () => {
    jsTreeInstance.refresh();
};

export const destroyJsTree = () => {
    if (jsTreeInstance) {
        jsTreeInstance.destroy();
    }
};
