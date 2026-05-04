<template>
    <div v-if="props.isLoading">
        <button type="button" class="btn animate-button w-100" :class="[props.size ? props.size : '']">
            <div class="spinner-border" style="width: 20px; height: 20px; border-width: 3px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            {{ props.name }}
        </button>
    </div>
    <div v-else>
        <button type="submit" class="btn animate-button w-100" :class="[props.size ? props.size : '']">
            {{ props.name }}
        </button>
    </div>
</template>
<script setup>
const props = defineProps(['isLoading', 'size', 'name']);
</script>
