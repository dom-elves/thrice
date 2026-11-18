<script setup lang="ts">
import { computed, onMounted } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
});

const value = computed(() => {
    return props.name.slice(0, -1);
});

const suit = computed(() => {
    return props.name.at(-1);
});

onMounted(() => {
    console.log('card');
});
</script>
<template>
    <div :class="suit">
        {{ value }}{{ suit }}
    </div>
</template>
<style>

.card {
    height:140px;
    width:100px;
    border-radius: var(--radius-lg);
    border: 4px solid;
    margin: 0px 5px;
    display: flex;
    justify-content: center;
    align-items: center;
}
 .h, .d {
    color: red;
    border-color:red;   
 }

 .c, .s {
    color: black;
    border-color: black;
 }
</style>