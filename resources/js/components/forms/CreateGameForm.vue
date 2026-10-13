<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputLabel from '@/components/forms/InputLabel.vue';
import TextInput from '@/components/forms/TextInput.vue';

</script>

<template>
    <Form
        class="flex flex-col items-center gap-2 p-4 w-1/2"
        action="/game/create"
        method="post"
        #default="{ errors }"
    >
        <div class="w-full">
            <InputLabel
                :for="'name'"
                :text="'Game Name'"
                class="text-center"
            />
            <TextInput :type="'name'" :name="'name'" />
            <p v-if="errors.name">{{ errors.name }}</p>
        </div>
        <div class="w-full">
            <InputLabel
                :for="'password'"
                :text="'Password'"
                class="text-center"
            />
            <TextInput :type="'password'" :name="'password'" />
            <p v-if="errors.password">{{ errors.password }}</p>
        </div>
        <div class="flex flex-row w-full justify-between">
            <div class="flex flex-row-reverse items-center">
                <InputLabel
                    :for="'private'"
                    :text="'Private Game?'"
                />
                <input
                    type="checkbox"
                    name="private"
                    value="1"
                />
            </div>
            <button type="submit">Create Game</button>
        </div>
    </Form>
</template>