import { ui, admin, user, photonModule } from '_/vuex/getters';
import { hideMassEditor, navigateToPage, navigateToEntry, showMassEditor, downloadFile } from '_/vuex/actions/advancedSearchActions';
import { massEdit } from '_/vuex/actions/adminActions';
import { bindValidatorToForm, resetValidator, destroyValidator, processErrors } from '_/services/formValidator';
import { getEntryFields } from '_/components/Admin/Entry/Entry.fields';
import { SelectBasic, InputText, RichText, Calendar, InputPassword, BooleanSwitch, FileInput, SelectTags } from '@/components';

export default {
    vuex: {
        getters: {
            ui,
            admin,
            user,
            photonModule
        },
        actions: {
            clearAdminErrors: ({dispatch}) => dispatch('CLEAR_ADMIN_ERRORS'),
            hideMassEditor,
            navigateToEntry,
            navigateToPage,
            massEdit,
            showMassEditor,
            downloadFile
        }
    },
    components: {
        SelectBasic,
        InputText,
        RichText,
        Calendar,
        InputPassword,
        BooleanSwitch,
        FileInput,
        SelectTags
    },
    data: function() {
        return {
            serverError: null,
            fields: []
        };
    },
    computed: {
        selectedModule: function() {
            return this.admin.selectedModule.table_name;
        },
        results: function() {
            if (!Object.keys(this.admin.entries[this.selectedModule]).length) {
                return null;
            }
            return this.admin.entries[this.selectedModule];
        },
        pagination: function() {
            return this.admin.entriesPagination[this.selectedModule];
        }
    },
    mounted: function() {
        this.$nextTick(function() {
            // Use uniform to style checkbox/radio elements
            $(this.$el).find('input[type="radio"], input[type="checkbox"]').uniform();
            // Bootstrap validator for this form
            this.validator = bindValidatorToForm({
                selector: '#mass-editor-form',
                onSubmit: () => {
                    this.clearAdminErrors();
                    this.massEdit()
                        .then((result) => {
                            if (!result) {
                                return;
                            }
                        });
                }
            });
            // Gets entry field options defined in Entry.fields
            this.fields = getEntryFields(this);
        });
    },
    beforeDestroy: function() {
        // On destroy, destroy the validator
        if (this.validator) {
            destroyValidator(this.validator);
        }
    },
    watch: {
        'admin.error.message': function() {
            // On user error message (failed menu change submit)
            this.$nextTick(() => {
                // Reset validator
                resetValidator(this.validator);
                // Process validator errors
                this.serverError = processErrors(this.validator, this.admin.error);
            });
        }
    }
};
