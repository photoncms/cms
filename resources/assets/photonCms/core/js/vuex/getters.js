// TODO: Delete this file once all getters are migrated to modules

export const ui = (state) => {
    return state;
};

export const sidebar = (state) => {
    return state.sidebar;
};

export const photonModule = (state) => {
    return state.photonModule;
};

export const photonModules = (state) => {
    // TODO: should consider refactoring this so it's handled better in store like other stores, not being initialized as null
    if (!state.photonModule.modules || !state.photonModule.modules.length) {
        return [];
    }
    return state.photonModule.modules;
};

export const categoryModules = (state) => {
    return state.photonModule.categoryModules;
};

export const generator = (state) => {
    return state.generator;
};

export const admin = (state) => {
    return state.admin;
};

export const entries = (state) => {
    return state.admin.entries;
};

export const menuEditor = (state) => {
    return state.menuEditor;
};

export const menuItemsEditor = (state) => {
    return state.menuItemsEditor;
};

export const itineraryWorkflow = (state) => {
    return state.itineraryWorkflow;
};

export const expenseReportsWorkflow = (state) => {
    return state.expenseReportsWorkflow;
};

export const bookNow = (state) => {
    return state.bookNow;
};
