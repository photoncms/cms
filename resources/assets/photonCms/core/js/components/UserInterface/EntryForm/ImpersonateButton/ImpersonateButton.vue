<template>
    <div class="form-group pull-left no-bottom-margin">
        <button type="button"
            @click="impersonate(userId)"
            class="btn btn-primary">{{ $t('admin.impersonateThisUser') }}</button>
    </div>
</template>

<script>
    import ImpersonateButton from './ImpersonateButton.js';
    export default ImpersonateButton;
</script>
