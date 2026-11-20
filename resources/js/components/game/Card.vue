<script setup lang="ts">
import { computed, onMounted } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    invisible: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const value = computed(() => {
    return props.name.slice(0, -1);
});

const suit = computed(() => {
    return props.name.at(-1);
});

const code = computed(() => {
    return {
        h: '\u2665',
        d: '\u2666',
        s: '\u2660',
        c: '\u2663',
    }[suit.value ?? ''];
});

// todo: when writing code to reveal cards, make sure to change this
// once logic is in place much later for dealing individual cards
// although, maybe that needs to be from the backend
const invisible = computed(() => {
    return props.invisible;
});

onMounted(() => {
    console.log('card', props.invisible);
});
</script>
<template>
    <div v-if="!invisible" class="card front" :class="suit">
        <p class="bold text-2xl">{{ value }}{{ code }}</p>
    </div>
    <div v-else class="card back">
        <p class="bold text-2xl">?</p>
    </div>
</template>
<style>
.card {
    height: 140px;
    width: 100px;
    border-radius: var(--radius-lg);
    border: 4px solid;
    margin: 0px 5px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.front {
    background-color: white;
}

.back {
    background-color: rgb(72, 0, 255);
    border-color: rgb(72, 0, 255);
    color: white;
}
.h,
.d {
    color: red;
    border-color: red;
}

.c,
.s {
    color: black;
    border-color: black;
}

@media (width <= 384px) {
    .card {
        height: 56px;
        width: 40px;
    }
}

@media (width <= 768px) {
    .card {
        height: 70px;
        width: 50px;
    }
}
</style>
