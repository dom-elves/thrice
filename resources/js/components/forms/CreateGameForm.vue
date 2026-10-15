<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputLabel from '@/components/forms/InputLabel.vue';
import TextInput from '@/components/forms/TextInput.vue';
</script>

<template>
    <div class="flex justify-center ">
        <Form
            class="w-1/2 flex-col items-center gap-2 p-4"
            action="/game/create"
            method="post"
            #default="{ errors }"
        >
            <div class="w-full">
                <InputLabel :for="'name'" :text="'Game Name'" class="text-center" />
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
            <div class="flex w-full flex-row justify-between">
                <div class="flex flex-row-reverse items-center">
                    <InputLabel :for="'private'" :text="'Private Game?'" />
                    <input type="checkbox" name="private" value="1" />
                </div>
                <button type="submit">Create Game</button>
            </div>
        </Form>
    </div>
</template>
