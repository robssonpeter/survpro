<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
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
                        <p class="text-3xl">{{ $page.props.survey_count }}</p>
                    </div>

                    <!-- Card 2: Survey Responses -->
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="text-lg font-semibold mb-2">Survey Responses</h2>
                        <p class="text-3xl">{{ $page.props.responses_count }}</p>
                    </div>

                    <!-- Card 3: Active Users -->
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="text-lg font-semibold mb-2">Active Users</h2>
                        <p class="text-3xl">45</p>
                    </div>

                    <!-- Recent Surveys Table -->
                    <div class="bg-white p-4 rounded-lg shadow-md col-span-3">
                        <h2 class="text-lg font-semibold mb-4">Recent Surveys</h2>
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="float-left">Title</th>
                                <th>Status</th>
                                <th>Respondents</th>
                                <th>Created At</th>
                                <th>Actions</th> <!-- New column for actions -->
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="survey in $page.props.surveys" class="py-2">
                                <td class="font-semibold text-gray-500"><Link :href="route('survey.modify', survey.hashslug)">{{ survey.title }}</Link></td>
                                <td>
                                    <br>
                                    <span v-if="survey.status === 'published'" :class="`bg-green-200 my-2 text-green-800 p-2 rounded-full`">Active</span>
                                    <span v-if="survey.status === 'closed'" :class="`bg-red-200 my-2 text-green-800 p-2 rounded-full`">Closed</span>
                                    <span v-if="survey.status === 'draft'" :class="`bg-gray-200 my-2 text-green-800 p-2 rounded-full`">Draft</span>
                                </td>
                                <td><span class="text-center">{{ survey.respondents }}</span></td>
                                <td>Oct 15, 2023</td>
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
                        <Link :href="route('surveys.create')" class="bg-blue-500 text-white py-2 px-4 rounded-md inline-block">Create New Survey</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
