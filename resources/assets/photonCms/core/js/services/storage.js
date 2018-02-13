
/**
 * Storage service object
 * @type {Object}
 */
export const storage = {

    /**
     * Declare all possible storage objects (these are used for bootstrapping)
     * @type {Object}
     */
    _data: {
        apiToken: null,
        licenseStatus: null,
        sidebarExtended: true,
    },

    /**
     * Sets prefix to use in local/session storage, to avoid conflicts with other local/session storage properties
     * if used on localhost since it's the same domain for multiple projects
     * @type {String}
     */
    prefix: 'photoncms_',

    /**
     * Botstrap method that runs on app initialization
     */
    bootstrap: function() {
        for (const key in this._data) {
            if (!this._data.hasOwnProperty(key)) {
                continue;
            }

            // For each key gets value from session or local storage
            // (second argument means get it from storage, not _data object)
            const value = this.get(key, true);

            // Sets value for current _data key
            this._data[key] = value;
        }

    },

    /**
     * Clears the data storage
     */
    clear: function() {
        for (let key in this._data) {
            if (this._data.hasOwnProperty(key)) {
                // Clear all keys in _data object
                this._data[key] = null;
            }
        }

        if (!Modernizr.localstorage) {
            return;
        }

        window.localStorage.clear();

        window.sessionStorage.clear();
    },

    /**
     * Saves value to selected key, with option to persist to localStorage
     * @param  {string} key
     * @param  {string} value
     * @param  {boolean} persist
     */
    save: function(key, value, persist) {
        // First sets it as _data object value
        this._data[key] = value;

        if (!Modernizr.localstorage) {
            return;
        }

        // If it should persist store it in localStorage
        if (persist) {
            window.localStorage.setItem(this.prefix + key, JSON.stringify(value));

            return;
        }

        // Else store it in sessionStorage
        window.sessionStorage.setItem(this.prefix + key, JSON.stringify(value));
    },

    /**
     * Removes value for selected key
     * @param  {string} key
     */
    remove: function(key) {
        // Does not delete _data keys since they might be needed for re-bootstrap, just sets them to null
        this._data[key] = null;

        if (!Modernizr.localstorage) {
            return;
        }

        window.sessionStorage.removeItem(this.prefix + key);

        window.localStorage.removeItem(this.prefix + key);
    },

    /**
     * Gets value for selected key, if bootstrap argument is true, does not get them from _data,
     * but from browser session or local storage
     * @param  {string} key
     * @param  {boolean} bootstrap
     * @return {mixed}
     */
    get: function(key, bootstrap) {
        // Only return from localStorage/cookies during bootstrap; Use in-memory storage instead
        if (!Modernizr.localstorage) {
            return this._data[key];
        }

        // If there is no such key in session or local storage (browser returns null in those cases)
        if (window.sessionStorage.getItem(this.prefix + key) === null
            && window.localStorage.getItem(this.prefix + key) === null) {
            // Return value from _data regardless of bootstrap argument
            return this._data[key];
        }

        // If bootstrap is true, always return from session or local storage
        if (bootstrap) {
            return JSON.parse(
                window.sessionStorage.getItem(this.prefix + key) || window.localStorage.getItem(this.prefix + key)
            );
        }

        // Else return from _data
        return this._data[key];
    }
};
