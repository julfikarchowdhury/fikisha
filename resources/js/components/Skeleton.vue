<!-- SkeletonLoader.vue -->
<template>
    <div class="skeleton-loader">
        <!-- Simulated content loading using skeleton boxes -->
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-image"></div>
    </div>
</template>

<script>
export default {
    name: 'SkeletonLoader',
}
</script>

<style scoped>
.skeleton-loader {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.skeleton {
    background-color: #e0e0e0;
    border-radius: 4px;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-title {
    width: 60%;
    height: 24px;
}

.skeleton-text {
    width: 100%;
    height: 16px;
}

.skeleton-image {
    width: 100%;
    height: 150px;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }

    100% {
        opacity: 1;
    }
}
</style>