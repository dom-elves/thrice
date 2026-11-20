<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { onMounted, computed, ref } from 'vue';
import Card from '@/components/game/Card.vue';
import type { PageProps, Hand } from '@/interfaces';

const page = usePage<PageProps>();
const game = page.props.game;
const gameUserCards = computed(() => page.props.gameUserCards);

</script>
<template>
    <div>
        <p>these are my cards</p>
        <div class="flex flex-row justify-center">
            <Card
                v-for="(card, index) in gameUserCards"
                :name="card"
                :key="index"
            />
        </div>
    </div>
</template>