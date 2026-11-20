<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { onMounted, computed, ref } from 'vue';
import HandCard from '@/components/game/HandCard.vue';
import Hole from '@/components/game/Hole.vue';
import Card from '@/components/game/Card.vue';
import type { PageProps, Hand } from '@/interfaces';

const page = usePage<PageProps>();
const game = page.props.game;
const gameUserCards = computed(() => page.props.gameUserCards);

const dealtCards = ref({
    bottom_card_1: gameUserCards.value?.[0] ?? '',
    bottom_card_2: gameUserCards.value?.[1] ?? '',
    middle_card_1: gameUserCards.value?.[2] ?? '',
    middle_card_2: gameUserCards.value?.[3] ?? '',
    top_card_1: gameUserCards.value?.[4] ?? '',
    top_card_2: gameUserCards.value?.[5] ?? '',
});

const selectedHand = ref({
    bottom_card_1: '',
    bottom_card_2: '',
    middle_card_1: '',
    middle_card_2: '',
    top_card_1: '',
    top_card_2: '',
});

const selectedCard = ref('');

function selectCard(card: string, index: string) {
    console.log('pick', card, index);
    selectedCard.value = card;
    dealtCards[index] = '';
}

function lockIn() {
    console.log('hand', selectedHand.value);
}

onMounted(() => {
    console.log('guc', gameUserCards.value);
});
</script>
<template>
    <div>
        <p>these are my cards</p>
        <div class="flex flex-row">
            <!-- todo: either emit event down or use a store -->
            <Hole
                v-for="(slot, hole) in selectedHand"
                @click="selectedHand[hole] = selectedCard"
                :key="hole"
                :hole="hole"
            >
            </Hole>
        </div>
        <div class="flex flex-row justify-center">
            <!-- separate parent to cover interactivity only applicable in hand cards -->
            <HandCard
                v-for="(card, index) in dealtCards"
                @click="selectCard(card, index)"
            >
                <Card :name="card" :key="index" />
            </HandCard>
        </div>
        <button @click="lockIn">lock in</button>
    </div>
</template>
