<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';



onMounted(() =>{

});

</script>

<template>
    <GuestLayout>
        <Form action="/register" method="post">
            <input type="name" name="name" class="border"/>
            <input type="email" name="email" class="border"/>
            <input type="password" name="password"  class="border"/>
            <input type="password" name="password_confirmation"  class="border"/>
            <button type="submit">Register</button>
        </Form>
    </GuestLayout>
</template>
