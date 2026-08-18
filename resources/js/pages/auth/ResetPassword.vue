<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import InputLabel from '@/components/forms/InputLabel.vue';
import TextInput from '@/components/forms/TextInput.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';

const props = defineProps<{
    token: string;
}>()

onMounted(() => {
    console.log(props.token);
});

</script>
<template>
    <GuestLayout>
        <Form
            action="/reset-password"
            method="post"
            #default="{
                errors,
            }"
        >
            <InputLabel :for="'email'" :text="'Email'"/>
            <TextInput :type="'email'" :name="'email'" />
            <p v-if="errors.email">{{ errors.email }}</p>
            <InputLabel :for="'password'" :text="'Password'"/>
            <TextInput :type="'password'" :name="'password'" />
            <p v-if="errors.password">{{ errors.password }}</p>
            <InputLabel :for="'password_confirmation'" :text="'Confirm Password'"/>
            <TextInput :type="'password'" :name="'password_confirmation'" />
            <p v-if="errors.password_confirmation">{{ errors.password_confirmation }}</p>
            <input type="hidden" name="token" :value="token"/>
            <button type="submit">Reset</button>
        </Form>
    </GuestLayout>
</template>