<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { computed, ref, onUnmounted, onMounted } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

interface PageProps {
    [key: string]: unknown;
    code: string;
    props: {
        auth: {
            user: object;
        };
    };
    session: {
        isReady: boolean;
    };
}

interface User {
    id: number;
    name: string;
    ready: boolean;
}

interface GameCreatedEvent {
    game: {
        id: number;
    };
}

interface UserToggleReadyEvent {
    status: boolean;
    user: {
        id: number;
        ready: boolean;
    };
}

const page = usePage<PageProps>();
const code = page.props.code;
const users = ref(<User[]>[]);
// todo: if i end up with a channel for game start signal
// change this to proper computed property and make it toggleable
// const isReady = ref<boolean>(false);
const readying = ref<boolean>(false);

const { channel } = useEchoPresence(
    `lobby.${code}`,
    '.game.created',
    (event: GameCreatedEvent) => {
        setTimeout(() => {
            router.visit(`/game/${event.game.id}`);
        }, 2000);
    },
);

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

const isReady = computed(() => {
    const user = users.value.find(
        (user) => user.id === page.props.auth.user.id,
    );

    return user?.ready ?? false;
});

useEchoPresence(
    `lobby.${code}`,
    '.player.toggleReady',
    (event: UserToggleReadyEvent) => {
        console.log('ev', event);
        const user = users.value.find((user) => user.id === event.user.id);

        if (user) {
            user.ready = event.status;
        }
    },
);

function toggleReady() {
    readying.value = true;
    router.post(
        `/lobby/${code}/ready`,
        {
            preserveState: true,
            preserveScroll: true,
            status: !isReady.value,
        },
        {
            onSuccess: (response) => {
                console.log('r', response);
                readying.value = false;
            },
            onError: (error) => {
                console.log(error);
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
    // console.log('prop', page.props);
    console.log('aaaa', isReady.value);
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
                    {{ user.name }} {{ user.ready }}
                </li>
            </ul>
            <button
                @click="toggleReady"
                class="m-4 cursor-pointer rounded border border-1 p-4"
                :class="isReady ? 'bg-green-300' : 'bg-blue-300'"
                :disabled="readying"
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
