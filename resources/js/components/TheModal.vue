<template>
    <teleport to="body">
        <transition name="fade">
            <div class="modal the-modal" v-if="modelValue">
                <transition name="zoom">
                    <div class="the-modal__container" v-if="modelValue">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">{{ heading }}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    @click="closeModal"></button>
                            </div>
                            <div class="modal-body">
                                <slot></slot>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
<script>
export default {
    props: {
        heading: {
            type: String,
            default: "Default Heading",
        },
        modelValue: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        closeModal() {
            this.$emit("update:modelValue", false);
        },
    },
};
</script>
<style>
.the-modal {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 999999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.the-modal__container {
    min-width: 50%;
    max-width: 80%;
    min-height: 222px;
    border-radius: 9px;
    box-shadow: 0 0 15px 5px rgb(0 0 0 / 20%);
    background: #fff;
}

.the-modal__container--lg {
    width: 555px;
}

.the-modal__header {
    font-size: 22px;
    font-weight: bold;
    color: var(--brand-color);
}

.the-modal__close {
    cursor: pointer;
    padding: 3px 9px;
    font-weight: bold;
}

.the-modal__close:hover {
    color: red;
}

.the-modal__body {
    padding: 44px;
    padding-top: 22px;
}
</style>
