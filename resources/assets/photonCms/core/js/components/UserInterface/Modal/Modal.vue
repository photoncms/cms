<template>
    <div v-show="show" :transition="transition">
        <div class="modal modal-vue" @click.self="clickMask">
            <div class="modal-dialog" :class="modalClass" ref="dialog">
                <div class="modal-content">
                    <!--Header-->
                    <div class="modal-header">
                        <slot name="header">
                            <a type="button" class="close" @click="cancel">&times;</a>
                            <h4 class="modal-title">
                                <slot name="title">
                                    {{title}}
                                </slot>
                            </h4>
                        </slot>
                    </div>
                    <!--Container-->
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <!--Footer-->
                    <div class="modal-footer">
                        <slot name="footer">
                            <button
                                type="button"
                                :class="cancelClass"
                                @click="cancel"
                                >
                                {{cancelText}}
                            </button>
                            <button
                                type="button"
                                :class="okClass"
                                @click="ok"
                                >
                                {{okText}}
                            </button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop in"></div>
    </div>
</template>

<script>
    import Modal from './Modal.js';
    export default Modal;
</script>
