<script setup lang="ts">
import { usePage, Form } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import InputLabel from '@/components/forms/InputLabel.vue';
import TextInput from '@/components/forms/TextInput.vue';
import Modal from '@/components/Modal.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const page = usePage();
const showModal = ref(false);
const user = page.props.auth.user;
const creatingGame = ref(false);
const joiningGame = ref(false);

function closeModal() {
    showModal.value = false;
    creatingGame.value = false;
    joiningGame.value = false;
}

watch(
    () => creatingGame.value,
    () => {
        if (creatingGame.value) {
            // perform request
        }
    },
);

onMounted(() => {
    console.log(user);
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="m-4">gz auth {{ user.name }}</div>
        <div>
            <button
                @click="
                    showModal = true;
                    creatingGame = true;
                "
                class="button mx-2 rounded border-1 border-[#1b1b18] bg-transparent p-2 text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
            >
                Create
            </button>
            <button
                @click="
                    showModal = true;
                    joiningGame = true;
                "
                class="button mx-2 rounded border-1 border-[#1b1b18] bg-transparent p-2 text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
            >
                Join
            </button>
        </div>
        <Modal :show="showModal" @close="closeModal">
            <div v-if="creatingGame">
                <Form
                    class="flex flex-col items-center justify-center gap-2 p-4"
                    action="/create-game"
                    method="post"
                    #default="{ errors }"
                >
                    <InputLabel
                        :for="'name'"
                        :text="'Game Name'"
                        class="text-center"
                    />
                    <TextInput :type="'name'" :name="'name'" />
                    <p v-if="errors.name">{{ errors.name }}</p>
                    <InputLabel
                        :for="'password'"
                        :text="'Password'"
                        class="text-center"
                    />
                    <TextInput :type="'password'" :name="'password'" />
                    <p v-if="errors.password">{{ errors.password }}</p>
                    <button type="submit">Create Game</button>
                </Form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
