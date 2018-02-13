import { config } from '_/config/config';

export const pLog = (...message) => {
    if (config.ENV.debug) {
        console.info('[Photon CMS info] ', message);
    }
};

export const pError = (...message) => {
    console.error('[Photon CMS error] ', message);
};

export const pWarn = (...message) => {
    console.warn('[Photon CMS warn] ', message);
};
