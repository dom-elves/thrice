<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { ref, onUnmounted, onMounted } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

interface PageProps {
    [key: string]: unknown;
    code: string;
    user: object;
    session: {
        isReady: boolean;
    };
}

interface User {
    id: number;
    name: string;
}

interface GameCreatedEvent {
    game: {
        id: number;
    };
}

const page = usePage<PageProps>();
const code = page.props.code;
const users = ref(<User[]>[]);
// todo: if i end up with a channel for game start signal
// change this to proper computed property and make it toggleable
const isReady = ref<boolean>(page.props.session.isReady ?? false);

const { channel } = useEchoPresence(
    `lobby.${code}`,
    '.game.created',
    (event: GameCreatedEvent) => {
        setTimeout(() => {
                router.visit(`/game/${event.game.id}`);
            }, 2000);
    },
);

// echo
channel()
    .here((activeUsers: User[]) => {
        users.value = activeUsers;
        console.log('here', users.value);
    })
    .joining((user: User) => {
        console.log('join', user);
    })
    .leaving((user: User) => {
        console.log('leave', user);
    })
    .error((error: unknown) => {
        console.error('e', error);
    });

function ready() {
    router.post(
        `/lobby/${code}/ready`,
        {
            preserveState: true,
            preserveScroll: true,
        },
        {
            onSuccess: (response) => {

                isReady.value = true;
            },
            onError: (error) => {

                isReady.value = false;
            },
        },
    );
}

function leaveLobby() {
    router.post(`/lobby/${code}/leave`, {
        preserveState: true,
        preserveScroll: true,
    });
}

onMounted(() => {
    console.log(code);
});
onUnmounted(() => {
    // todo: think of a better way to detect leaving page
    // as this is being called after redirect to game, etc
    // leaveLobby();
});
</script>
<template>
    <AuthenticatedLayout>
        <div class="flex flex-col">
            <p>welcome to the lobby</p>
            <p>here are the users:</p>
            <ul>
                <li v-for="user in users" :key="user.id">
                    {{ user.name }}
                </li>
            </ul>
            <button
                @click="ready"
                class="m-4 rounded border border-1 p-4"
                :class="
                    isReady
                        ? 'cursor-not-allowed bg-green-300'
                        : 'cursor-pointer bg-blue-300'
                "
                :disabled="isReady"
            >
                ready
            </button>
            <button
                @click="leaveLobby"
                class="m-4 rounded border border-1 bg-red-300 p-4"
            >
                leave lobby
            </button>
            <p>{{ page.flash.message }}</p>
        </div>
    </AuthenticatedLayout>
</template>
