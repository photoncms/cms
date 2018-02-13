// Import config from JSON file
const photonConfig = require('~/config/config.json');

// Initialize config object
export let config = { };

// Helper function to check if passed environment hostname matches actual hostname
const _matchEnv = (envHostname) => {
    if (document.location.hostname !== envHostname) {
        // If no match return
        return;
    }
    // Else assing environment to config.ENV object
    config.ENV = photonConfig.env[envHostname];
};


// Check all environments in config JSON
for (const envHostname in photonConfig.env) {
    if (photonConfig.env.hasOwnProperty(envHostname)) {
        // Check if any environment matches current hostname
        _matchEnv(envHostname);
    }
}

if (!config.ENV) {
    // If no suitable environment was found for current hostname
    // check if default env was provided in JSON
    if (!photonConfig.env.default) {
        // If no default throw error
        throw 'Invalid Photon config file. No default environment set.';
    }
    // Else assing default environment to config.ENV object
    config.ENV = photonConfig.env.default;
}
