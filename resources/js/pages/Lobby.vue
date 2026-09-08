<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { ref, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

interface PageProps {
    [key: string]: unknown;
    code: string;
    user: object;
}

const page = usePage<PageProps>();
const code = page.props.code;
const users = ref([]);

const { channel } = useEchoPresence(
    `lobby.${code}`,
    '', // no custom event to listen for — presence hooks below handle membership
    () => {},
);

channel()
    .here((activeUsers) => {
        users.value = activeUsers;
        console.log(users.value, ' is jere');
    })
    .joining((user) => {
        console.log(user.name, ' joined');
    })
    .leaving((user) => {
        console.log(user.name, ' left');
    })
    .error((error) => {
        console.error('e', error);
    });

function leaveLobby() {
    router.post(`/lobby/${code}/leave`, {
        preserveState: true,
        preserveScroll: true,
        code: code,
    });
}

onUnmounted(() => {
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
                @click="leaveLobby"
                class="m-4 rounded border border-1 bg-red-300 p-4"
            >
                leave lobby
            </button>
        </div>
    </AuthenticatedLayout>
</template>
