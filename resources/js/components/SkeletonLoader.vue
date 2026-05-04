<template>
    <slot></slot>
    <div class="loading" v-if="loading">
        <img src="../../images/spinner1.gif" alt="Loading" /><br>
    </div>
</template>

<script>
export default {
    props: {
        loading: {
            type: Boolean,
            default: true,
        },
        height: {
            type: String,
        },
        width: {
            type: String,
        },
        borderRadius: {
            type: String,
        },
    }
};
</script>
<style>
body {
    position: relative;
}

.loading {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 999999;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #fafafb !important;
}

.loading img {
    width: 200px;
}
</style>
