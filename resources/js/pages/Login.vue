<script setup lang="ts">
import { Form, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import TextInput from '@/components/forms/TextInput.vue';
import InputLabel from '@/components/forms/InputLabel.vue';

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
            <InputLabel :for="'email'" :text="'Email'"/>
            <TextInput :type="'email'" :name="'email'"/>
            <p v-if="errors.email">{{ errors.email }}</p>
            <InputLabel :for="'password'" :text="'Password'"/>
            <TextInput :type="'password'" :name="'password'"/>
            <button type="submit">Login</button>
        </Form>
        <p v-if="(page.props.session as any)?.status">{{ (page.props.session as any).status }} Please log in with your new password.</p>
    </GuestLayout>
</template>
