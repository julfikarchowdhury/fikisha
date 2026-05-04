<script setup>
defineProps(["value", "required"]);
</script>

<template>
    <label class="form-label">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span class="text-danger" v-if="required">{{ required }}</span></label
    >
</template>
