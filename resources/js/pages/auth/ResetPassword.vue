<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';
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
            <label for="email">Email</label>
            <input type="email" name="email" class="border"/>
            <p v-if="errors.email">{{ errors.email }}</p>
            <label for="password">password</label>
            <input type="password" name="password" class="border"/>
            <p v-if="errors.password">{{ errors.password }}</p>
            <label for="password_confirmation">password_confirmation</label>
            <input type="password" name="password_confirmation" class="border"/>
            <p v-if="errors.password_confirmation">{{ errors.password_confirmation }}</p>
            <input type="hidden" name="token" :value="token"/>
            <button type="submit">Reset</button>
        </Form>
    </GuestLayout>
</template>