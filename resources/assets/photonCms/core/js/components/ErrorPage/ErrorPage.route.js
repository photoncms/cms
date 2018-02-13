import { store } from '@/loader';
import { setError, set404State } from '_/vuex/actions/errorPageActions';

export default {
    waitForData: true,
    data: function(transition) {
        if (!transition.to.params || !transition.to.params.errorName) {
            // If there are no params set up default 404 error page state
            set404State(store);

            return transition.next();
        }

        if (transition.to.params.errorName === 'api-not-accessible') {
            // If api not responding setup 503 error page state
            setError(store, {
                errorCode: '503',
                errorIcon: 'fa fa-frown',
                errorText: 'API Service Unavailable.',
            });
        }

        if (transition.to.params.errorName === 'user-logged-out') {
            // If a user has been logged out
            setError(store, {
                errorCode: '401',
                errorIcon: 'fa fa-frown',
                errorText: 'You are not logged in.',
            });
        }
        // Continue with transition
        transition.next();
    }
};
