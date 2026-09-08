<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

interface PageProps {
    [key: string]: unknown;
    code: string;
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
    })
    .joining((user) => {
        console.log(user.name);
    })
    .leaving((user) => {
        console.log(user.name);
    })
    .error((error) => {
        console.error(error);
    });

</script>
<template>
    <AuthenticatedLayout>
        <div class="flex flex-col">
            <p>welcome to the lobby</p>
            <p>here are the users:</p>
            <ul>
                <li v-for="user in users">
                    {{ user.name }}
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
