<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex-grow">Surveys</h2>
                <Link :href="route('surveys.create')" class="bg-blue-500 text-white py-2 px-4 rounded-md">Create New</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto card bg-white shadow-md text-neutral">
                <!-- List of Surveys -->
                <div v-if="surveys.length === 0" class="bg-white p-2 text-center">
                    <span>Currently no survey has been made</span>
                </div>
                <div v-if="surveys.length > 0" class="mb-4">
                    <div class="grid grid-cols-5 gap-4 py-4 border-b-accent border-b sm:px-6 lg:px-8">
                        <div class="font-bold col-span-2">Title</div>
                        <div class="font-bold text-center">Respondents</div>
                        <div class="font-bold">Due Date</div>
                        <div class="font-bold">Actions</div>
                    </div>
                    <div v-for="survey in surveys" :key="survey.id" class="grid grid-cols-5 gap-4 py-2">
                        <div class="sm:px-6 lg:px-8 col-span-2"><Link :href="route('survey.modify', survey.hashslug)">{{ survey.title }}</Link></div>
                        <div class="text-center"><span>{{ survey.respondents }}</span></div>
                        <div>{{ survey.due_date }}</div>
                        <div class="flex space-x-8">
                            <button class="btn btn-ghost" @click="editSurvey(survey)"><Link :href="route('survey.modify', survey.hashslug)">Edit</Link></button>

                            <button class="btn btn-danger" @click="deleteSurvey(survey)">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {Link} from "@inertiajs/vue3";
export default {
    components: [
      Link
    ],
    data() {
        return {
            surveys: [], // An array to store the list of surveys
            newSurvey: {
                text: '',
                created_at: '',
                updated_at: '',
            },
        };
    },
    mounted() {
        // Fetch surveys from your API and populate the 'surveys' array
        // You can use Inertia's inertia.get() here
        this.surveys = this.$page.props.surveys
    },
    methods: {
        addSurvey() {
            // Send a request to your API to add a new survey with this.newSurveyText
            // After adding, refresh the list of surveys
            // You can use Inertia's inertia.post() here
        },
        editSurvey(survey) {
            // Implement the edit functionality to update a survey
            // You can open a modal or redirect to an edit page
        },
        async deleteSurvey(survey) {
            try {
                // Send a DELETE request to your Laravel backend to delete the survey
                await this.$inertia.delete(route("survey.delete", survey.survey_id));

                // If the request is successful, remove the survey from the list
                this.surveys = this.surveys.filter((s) => s.survey_id !== survey.survey_id);
            } catch (error) {
                // Handle any errors that occur during the delete request
                console.error("Error while deleting survey:", error);
            }
        },
    },
};
</script>

<style scoped>

</style>
