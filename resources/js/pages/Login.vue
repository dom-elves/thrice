<script setup lang="ts">
import { Form, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';

const page = usePage();

onMounted(() =>{

});

</script>



<template>
    <GuestLayout>
        <Form
            action="/login"
            method="post"
            #default="{
                errors,
            }"
        >
            <input type="email" name="email" class="border"/>
            <p v-if="errors.email">{{ errors.email }}</p>
            <input type="password" name="password"  class="border"/>
            <button type="submit">Login</button>
        </Form>
        <p v-if="(page.props.session as any)?.status">{{ (page.props.session as any).status }} Please log in with your new password.</p>
    </GuestLayout>
</template>
