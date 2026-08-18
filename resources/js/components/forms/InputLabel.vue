<script setup lang="ts">

const props = defineProps<{
    for: string;
    text: string;
}>()

</script>

<template>
    <label
        class="block text-sm font-medium text-gray-700 w-full"
        :for="props.for"
    >
        <span v-if="text">{{ text }}</span>
        <span v-else><slot /></span>
    </label>
</template>
