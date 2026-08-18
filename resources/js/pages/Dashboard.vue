<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import Modal from '@/components/Modal.vue';

const page = usePage();
const showModal = ref(false)
const user = page.props.auth.user;
const creatingGame = ref(false);
const joiningGame = ref(false);

function closeModal() {
    showModal.value = false;
    creatingGame.value = false;
    joiningGame.value = false;
}

onMounted(() => {
    console.log(user);
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="m-4">gz auth {{ user.name }}</div>
        <div>
            <button
                @click="showModal = true; creatingGame = true"
                class="button mx-2 rounded border-1 border-[#1b1b18] bg-transparent p-2 text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
            >
                Create
            </button>
            <button
                @click="showModal = true; joiningGame = true"
                class="button mx-2 rounded border-1 border-[#1b1b18] bg-transparent p-2 text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
            >
                Join
            </button>
        </div>
        <Modal :show="showModal" @close="closeModal">
            <p v-if="creatingGame">Creating..</p>
            <p v-if="joiningGame">Joining..</p>
        </Modal>
    </AuthenticatedLayout>
</template>
