<script setup>
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">

            <!-- Page Heading -->
            <header class="bg-white shadow" >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 text-gray-600 mt-2">
                    <div class="card bg-white">
                        hello
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
