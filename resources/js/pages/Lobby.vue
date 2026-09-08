<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoNotification } from '@laravel/echo-vue';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

</script>
<template>
    <AuthenticatedLayout>
        <div class="flex flex-col">
            <p>welcome to the lobby</p>
            <p>here are the users:</p>
            <ul>
                <li></li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
