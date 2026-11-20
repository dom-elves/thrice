<script setup lang="ts">
const props = defineProps({
    hole: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: false,
        default: null,
    },
});
</script>
<template>
    <div>
        <div class="hole">
            <p v-if="value">{{ value }}</p>
        </div>
    </div>
</template>
<style>
.hole {
    background-color: white;
    height: 140px;
    width: 100px;
    border-radius: var(--radius-lg);
    border: 4px solid green;
    margin: 0px 5px;
    display: flex;
    justify-content: center;
    align-items: center;
}

@media (width <= 384px) {
    .hole {
        height: 56px;
        width: 40px;
    }
}

@media (width <= 768px) {
    .hole {
        height: 70px;
        width: 50px;
    }
}
</style>
