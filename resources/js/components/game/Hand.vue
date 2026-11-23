<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, computed, ref, mergeProps } from 'vue';
import HandCard from '@/components/game/HandCard.vue';
import Hole from '@/components/game/Hole.vue';
import Card from '@/components/game/Card.vue';
import type { PageProps  } from '@/interfaces';

const page = usePage<PageProps>();
const game = page.props.game;
const gameUserCards = computed(() => page.props.gameUserCards);

type CardSlot =
    | 'bottom_card_1' | 'bottom_card_2'
    | 'middle_card_1' | 'middle_card_2'
    | 'top_card_1' | 'top_card_2';

const dealtCards = ref<Record<CardSlot, string>>({
    bottom_card_1: gameUserCards.value?.[0] ?? '',
    bottom_card_2: gameUserCards.value?.[1] ?? '',
    middle_card_1: gameUserCards.value?.[2] ?? '',
    middle_card_2: gameUserCards.value?.[3] ?? '',
    top_card_1: gameUserCards.value?.[4] ?? '',
    top_card_2: gameUserCards.value?.[5] ?? '',
});

const selectedSlot = ref<CardSlot | null>(null);

function selectCard(slot: CardSlot) {
    if (selectedSlot.value === null) {
        selectedSlot.value = slot;
        return;
    }

    // deselect the card
    if (selectedSlot.value === slot) {
        selectedSlot.value = null;
        return;
    }

    // set the slot, then the target, and swap, reset slot
    const first = selectedSlot.value;
    const temp = dealtCards.value[first];
    dealtCards.value[first] = dealtCards.value[slot];
    dealtCards.value[slot] = temp;
    selectedSlot.value = null;
}

function lockIn() {
    console.log('hand', dealtCards.value);
}

onMounted(() => {
    console.log('g', game);
});
</script>
<template>
    <div class="m-2">
        <div class="flex flex-row justify-center">
            <Hole
                v-for="(card, key) in dealtCards"
                :key="key"
                :row="key.slice(0, -7)"
            >
                <HandCard
                    @click="selectCard(key)"
                    style="border-radius: var(--radius-lg)"
                    :class="{ 'ring-4 ring-yellow-400': selectedSlot === key }"
                >
                    <Card :name="card" />
                </HandCard>
            </Hole>
        </div>
        <button @click="lockIn" class="bg-green-400 p-4 rounded">lock in</button>
    </div>
</template>
