<script setup lang="ts">
const props = defineProps<{
    type: string;
    name: string;
}>();
</script>

<template>
    <input
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :type="props.type"
        :name="props.name"
    />
</template>
