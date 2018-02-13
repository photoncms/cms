import { store } from '@/loader';
import { confirmEmailChange } from '_/vuex/actions/userActions';
import { errorTypes } from '_/services/errorTypes';

export default {
    waitForData: true,
    data: function(transition) {
        // Parse emailToken from url
        const emailToken = transition.to.params.emailToken;
        const userId = transition.to.params.userId;
        // Try confirming email address using emailToken
        confirmEmailChange(store, {
                emailToken,
                userId
            })
            .then((message) => {
                if (message === 'EMAIL_ADDRESS_CHANGED') {
                    // If email confirmed by API pass the message as data prop to component
                    return transition.next({
                        emailConfirmed: message
                    });
                }
                // If email NOT confirmed by API pass the error as data prop to component,
                // either as mapped error message or raw if no error translation found
                return transition.next({
                    confirmationError: errorTypes[message] || message
                });
            });
    }
};
