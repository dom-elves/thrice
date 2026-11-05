<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';
import type { PageProps } from '@/interfaces';

const page = usePage<PageProps>();
const game = page.props.game;
const gameUser = computed(() => page.props.gameUser);

const seated = computed(() => {
    return !!gameUser.value &&
    gameUser.value.in_game &&
    String(gameUser.value.id) === String(props.guid)
})

const empty = computed(() => !props.guid);

const props = defineProps({
    seat: {
        type: String,
        required: true,
    },
    guid: {
        type: String,
        required: false,
    },
});

onMounted(() => {
    console.log('here', seated);
});

function sit() {
    console.log('player', gameUser.value?.id, 'sitting at seat', props.seat);
    router.post(
        `/game/${game.code}/sit`,
        {
            'seat': props.seat
        },
        {
            only: ['gameUser'],
            preserveScroll: true,
            preserveState: true,
        },
    );
}

function stand() {
    console.log('player', gameUser.value?.id,'standing from seat', props.seat);
    router.post(
        `/game/${game.code}/stand`,
        {
            'seat': props.seat
        },
        {
            only: ['gameUser'],
            preserveScroll: true,
            preserveState: true,
        },
    );
}
</script>
<template>
    <div>
        <p>{{ props.seat }}</p>
        <p>Balance: {{ gameUser?.start_balance }}</p>
    
        <button
            v-if="seated"
            @click="stand"
            class="m-4 rounded border border-1 bg-blue-300 p-4"
        >
            stand up
        </button>
        <button
            v-else-if="empty"
            @click="sit"
            class="m-4 rounded border border-1 bg-green-300 p-4"
            :disabled="true"
        >
            sit down
        </button>
    </div>
</template>
