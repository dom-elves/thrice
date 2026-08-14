<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';


onMounted(() =>{
    console.log(props.auth.user);
});

</script>



<template>
    <div>
        gz auth
    </div>
</template>
