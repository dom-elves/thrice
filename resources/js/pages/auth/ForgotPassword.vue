<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputLabel from '@/components/forms/InputLabel.vue';
import TextInput from '@/components/forms/TextInput.vue';
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
            <InputLabel :for="'email'" :text="'Enter your email to reset your password'"/>
            <TextInput :type="'email'" :name="'email'" />
            <p v-if="errors.email">{{ errors.email }}</p>
            <button type="submit">Reset</button>
        </Form>
    </GuestLayout>
</template>