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
            user: {
                id: number;
                name: string;
            }
        };
    };
    session: {
        isReady: boolean;
    };
}

// this is for a user as in the one that is in the users array
// which can be anyone in the lobby
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

// simple refs for page, lobby code and present users
const page = usePage<PageProps>();
const code = page.props.code;
const users = ref(<User[]>[]);
const readying = ref<boolean>(false);

// computed property that is solely for button colour
const isReady = computed(() => {
    const user = users.value.find(
        (user: User) => user.id === page.props.auth.user.id,
    );

    return user?.ready ?? false;
});

const { channel } = useEchoPresence(
    `lobby.${code}`,
    '.game.created',
    (event: GameCreatedEvent) => {
        setTimeout(() => {
            // todo: lock everything and set a loading thing
            router.visit(`/game/${event.game.id}`);
        }, 2000);
    },
);

/**
 * .here() runs once when a user joins
 * others are self explainatory
 */
channel()
    .here((activeUsers: User[]) => {
        users.value = activeUsers;
    })
    .joining((user: User) => {
        users.value.push(user);
    })
    .leaving((user: User) => {
        users.value = users.value.filter((u) => u.id !== user.id);
    })
    .error((error: unknown) => {
        console.error('e', error);
    });

/**
 * Channel for listening to toggleReady events
 */
useEchoPresence(
    `lobby.${code}`,
    '.user.toggleReady',
    (event: UserToggleReadyEvent) => {
        const user = users.value.find((user) => user.id === event.user.id);

        if (user) {
            user.ready = event.status;
            readying.value = false;
        }
    },
);

/**
 * toggles user ready status in redis
 */
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
            },
            onError: (error) => {
                console.log('e', error);
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
    // aaa
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
                    {{ user.name }} {{ user.ready ? 'ready!' : 'not ready' }}
                </li>
            </ul>
            <button
                @click="toggleReady"
                class="m-4 cursor-pointer rounded border border-1 p-4"
                :class="isReady ? 'bg-green-300' : 'bg-blue-300'"
                :disabled="readying"
            >
                {{ readying ? '...waiting' : 'ready'}}
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
