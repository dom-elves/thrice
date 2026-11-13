<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted, computed, ref } from 'vue';
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
});

const gameUser = computed(() => page.props.gameUser);
const seatEmpty = computed(() => props.seat?.length == 0);
const satHere = computed(() => {
    return gameUser.value
        ? gameUser.value?.user_id == props.seat?.user_id
        : false;
});

const errors = ref(page.props.errors.position || null);

onMounted(() => {
    console.log(props.position, seatEmpty.value, satHere.value)
});

function sit() {
    router.post(
        `/game/${game.code}/sit`,
        {
            position: props.position,
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
    router.post(
        `/game/${game.code}/stand`,
        {
            position: props.position,
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
        <p>seat {{ props.position + 1 }}</p>
        <div v-if="seatEmpty">
            <button
                @click="sit"
                :disabled="gameUser?.in_game"
                class="m-4 rounded border border-1 p-4"
                :class="
                    gameUser?.in_game
                        ? 'cursor-not-allowed bg-gray-400'
                        : 'bg-green-300'
                "
            >
                sit down
            </button>
        </div>
        <div v-if="!seatEmpty">
            <button
                v-if="satHere"
                @click="stand"
                class="m-4 rounded border border-1 bg-blue-300 p-4"
            >
                stand up
            </button>
            <p v-else>taken by playerid {{ props.seat?.user_id }}</p>
        </div>
        <p v-if="errors">{{ errors }}</p>
    </div>
</template>
