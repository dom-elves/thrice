<script setup lang="ts">
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const user = page.props.auth.user;

onMounted(() => {
    console.log(user);
});

</script>



<template>
    <div>
        gz auth {{ user.name }}
    </div>
</template>
