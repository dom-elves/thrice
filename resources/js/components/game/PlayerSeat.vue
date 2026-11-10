<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';
import type { PageProps } from '@/interfaces';

const page = usePage<PageProps>();
const game = page.props.game;

const props = defineProps({
    position: {
        type: Number,
        required: true,
    },
    seat: {
        type: Object,
        required: false,
    },
    // guid: {
    //     type: String,
    //     required: false,
    // },
});

const gameUser = computed(() => page.props.gameUser);

const seated = computed(() => {
    return !!gameUser.value &&
    gameUser.value.in_game &&
    String(gameUser.value.user_id) === String(props.seat?.user_id)
})

const empty = computed(() => props.seat?.length === 0);

onMounted(() => {
    // console.log(props.guid);
});

function sit() {
    console.log('player', gameUser.value?.id, 'sitting at seat', props.position);
    router.post(
        `/game/${game.code}/sit`,
        {
            'position': props.position
        },
        {
            only: ['gameUser'],
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                console.log('successfully sat down at seat', props.position);
            },
        },
    );
}

function stand() {
    console.log('player', gameUser.value?.id,'standing from seat', props.position);
    router.post(
        `/game/${game.code}/stand`,
        {
            'position': props.position
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
        <p>seat {{ props.position }}</p>
        <button
            v-if="empty"
            @click="sit"
            :disabled="gameUser?.in_game"
            class="m-4 rounded border border-1 p-4"
            :class="gameUser?.in_game ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-300'"
        >
            sit down
        </button>
        <div v-else-if="gameUser?.in_game">
            <p>taken by playerid {{ gameUser?.id}}</p>
            <button
                @click="stand"
                class="m-4 rounded border border-1 bg-blue-300 p-4"
            >
                stand up
            </button>
        </div>
    </div>
</template>
