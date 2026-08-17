<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';


onMounted(() =>{

});

</script>

<template>
    <div>
        <Form action="/register" method="post">
            <input type="name" name="name" class="border"/>
            <input type="email" name="email" class="border"/>
            <input type="password" name="password"  class="border"/>
            <input type="password" name="password_confirmation"  class="border"/>
            <button type="submit">Register</button>
        </Form>
    </div>
</template>
