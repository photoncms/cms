<template>
    <div>
        <button
            @click="extensionCall({ url: admin.editedEntry.extensions.Cancel })"
            class="btn btn-warning"
            type="button"
            v-if="
                admin.editedEntry.invitation_status
                && admin.editedEntry.invitation_status.system_name !== 'canceled'
                && admin.editedEntry.permission_control.crud.update
                && admin.editorMode === 'edit'">
            {{ $t('admin.cancelInvitation') }}
        </button>
        <button
            @click="extensionCall({ url:admin.editedEntry.extensions.Resend })"
            type="button" class="btn btn-primary"
            v-if="
                admin.editedEntry.permission_control
                && admin.editedEntry.permission_control.crud.update
                && admin.editorMode === 'edit'"
            >
            {{ $t('admin.resendInvitation') }}
        </button>
    </div>
</template>

<script>
    import InvitationsButtons from './InvitationsButtons.js';
    export default InvitationsButtons;
</script>
