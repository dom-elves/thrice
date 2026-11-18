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
            <Card v-for="card in cards" :name="card" />
        </div>
    </div>
</template>
