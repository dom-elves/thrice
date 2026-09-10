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

const page = usePage<PageProps>();
const code = page.props.code;
const users = ref(<User[]>[]);
// todo: if i end up with a channel for game start signal
// change this to proper computed property and make it toggleable
const isReady = ref<boolean>(page.props.session.isReady ?? false);

const { channel } = useEchoPresence(
    `lobby.${code}`,
    '', // no custom event to listen for — presence hooks below handle membership
    () => {},
);

// echo
channel()
    .here((activeUsers: User[]) => {
        users.value = activeUsers;
        console.log(users.value, ' is jere');
        console.log();
    })
    .joining((user: User) => {
        console.log(user.name, ' joined');
    })
    .leaving((user: User) => {
        console.log(user.name, ' left');
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
                console.log('r', response);
                isReady.value = true;
            },
            onError: (error) => {
                console.log(error);
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
    console.log(page.props);
});
onUnmounted(() => {
    console.log('random unmount');
    leaveLobby();
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
