// import { store } from '@/loader';

// import { pError } from '_/helpers/logger';

export default {
    waitForData: true,
    data: function(transition) {
        store.dispatch('photonModule/getPhotonModules') // Get list of photon modules
            .then(transition.next) // On success allow transition
            .catch((response) => {
                pError(response);

                transition.abort();
            });
    }
};
