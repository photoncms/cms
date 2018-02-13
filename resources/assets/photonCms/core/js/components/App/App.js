import { mapActions } from 'vuex';

export default {
    methods: {
        handleWindowResize: function() { // On App ready, start monitoring resize events
            let resizeEnd;

            $(window).resize(() => {
                clearTimeout(resizeEnd);

                resizeEnd = setTimeout(() => { // Throttle resize using timeout
                    this.bodyResize($('body').width()); // Activate bodyResize action with new body width as payload
                }, 50);
            });
        },
        ...mapActions('ui', [
            'bodyResize'
        ])
    },
    mounted: function() {
        this.$nextTick(function() {
            this.handleWindowResize();
        });
    }
};
