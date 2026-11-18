<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/interfaces.ts';

const page = usePage<PageProps>();
const board = page.props.board;

</script>
<template>
    <div>
        {{ board }}
    </div>
</template>