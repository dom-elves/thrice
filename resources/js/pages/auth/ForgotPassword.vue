<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';


</script>
<template>
    <GuestLayout>
        <Form 
            action="/forgot-password" 
            method="post"
            #default="{
                errors,
            }"
        >
            <label for="email">Enter your email to reset your password</label>
            <input type="email" name="email" class="border"/>
            <p v-if="errors.email">{{ errors.email }}</p>
            <button type="submit">Reset</button>
        </Form>
    </GuestLayout>
</template>