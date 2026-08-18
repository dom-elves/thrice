<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import TextInput from '@/components/forms/TextInput.vue';
import InputLabel from '@/components/forms/InputLabel.vue';


onMounted(() =>{

});

</script>

<template>
    <GuestLayout>
        <Form action="/register" method="post">
            <InputLabel :for="'name'" :text="'Name'"/>
            <TextInput :type="'name'" name="name" class="border"/>
            <InputLabel :for="'email'" :text="'Email'"/>
            <TextInput :type="'email'" name="email" class="border"/>
            <InputLabel :for="'password'" :text="'Password'"/>
            <TextInput :type="'password'" name="password" class="border"/>
            <InputLabel :for="'password_confirmation'" :text="'Confirm Password'"/>
            <TextInput :type="'password'" name="password_confirmation" class="border"/>
            <button type="submit">Register</button>
        </Form>
    </GuestLayout>
</template>
