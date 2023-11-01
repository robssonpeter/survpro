<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Card 1: Total Surveys -->
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="text-lg font-semibold mb-2">Total Surveys</h2>
                        <p class="text-3xl">235</p>
                    </div>

                    <!-- Card 2: Survey Responses -->
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="text-lg font-semibold mb-2">Survey Responses</h2>
                        <p class="text-3xl">4,562</p>
                    </div>

                    <!-- Card 3: Active Users -->
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="text-lg font-semibold mb-2">Active Users</h2>
                        <p class="text-3xl">45</p>
                    </div>

                    <!-- Recent Surveys Table -->
                    <div class="bg-white p-4 rounded-lg shadow-md col-span-3">
                        <h2 class="text-lg font-semibold mb-4">Recent Surveys</h2>
                        <table class="w-full">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Responses</th>
                                <th>Created At</th>
                                <th>Actions</th> <!-- New column for actions -->
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="#">Survey 1</a></td>
                                <td><span class="bg-green-200 text-green-800 p-2 rounded-full">Active</span></td>
                                <td>124</td>
                                <td>Oct 15, 2023</td>
                                <td>
                                    <a href="#" class="text-blue-600">Edit</a>
                                    <a href="#" class="text-red-600 ml-2">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="#">Survey 2</a></td>
                                <td><span class="bg-red-200 text-red-800 p-2 rounded-full">Closed</span></td>
                                <td>432</td>
                                <td>Oct 10, 2023</td>
                                <td>
                                    <a href="#" class="text-blue-600">Edit</a>
                                    <a href="#" class="text-red-600 ml-2">Delete</a>
                                </td>
                            </tr>
                            <!-- Add more survey rows as needed -->
                            </tbody>
                        </table>
                    </div>

                    <!-- New Surveys Button -->
                    <div class="col-span-3">
                        <a href="#" class="bg-blue-500 text-white py-2 px-4 rounded-md inline-block">Create New Survey</a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
