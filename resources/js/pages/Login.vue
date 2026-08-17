<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';


onMounted(() =>{

});

</script>



<template>
    <div>
        <Form
            action="/login"
            method="post"
            #default="{
                errors,
            }"
        >
            <input type="email" name="email" class="border"/>
            <p v-if="errors.email">{{ errors.email }}</p>
            <input type="password" name="password"  class="border"/>
            <button type="submit">Login</button>
        </Form>
    </div>
</template>
