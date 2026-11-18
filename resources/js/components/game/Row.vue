<script setup lang="ts">
import Card from '@/components/game/Card.vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    cards: {
        type: Object,
        required: true,
    },
});
</script>
<template>
    <div>
        <p>this is row {{ props.name }}</p>
        <div class="flex flex-row">
            <Card v-for="(card, index) in cards" :name="card" :key="index" />
        </div>
    </div>
</template>
