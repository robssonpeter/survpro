<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Survey Questions</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- List of Questions -->
                <div v-if="questions.length > 0" class="mb-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="font-bold">ID</div>
                        <div class="font-bold">Question Text</div>
                        <div class="font-bold">Actions</div>
                    </div>
                    <div v-for="question in questions" :key="question.id" class="grid grid-cols-3 gap-4">
                        <div>{{ question.id }}</div>
                        <div>{{ question.text }}</div>
                        <div>
                            <button @click="editQuestion(question)">Edit</button>
                            <button @click="deleteQuestion(question)">Delete</button>
                        </div>
                    </div>
                </div>

                <!-- Add New Question -->
                <div>
                    <div class="text-xl font-semibold mb-2">Add New Question</div>
                    <form @submit.prevent="addQuestion">
                        <div class="mb-2">
                            <label for="newQuestionText">Question Text:</label>
                            <input type="text" id="newQuestionText" v-model="newQuestion.text" />
                        </div>
                        <div class="mb-2">
                            <label for="newQuestionCreatedAt">Created At:</label>
                            <input type="text" id="newQuestionCreatedAt" v-model="newQuestion.created_at" />
                        </div>
                        <div class="mb-2">
                            <label for="newQuestionUpdatedAt">Updated At:</label>
                            <input type="text" id="newQuestionUpdatedAt" v-model="newQuestion.updated_at" />
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md">Add Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            questions: [], // An array to store the list of questions
            newQuestion: {
                text: '',
                created_at: '',
                updated_at: '',
            },
        };
    },
    mounted() {
        // Fetch questions from your API and populate the 'questions' array
        // You can use Inertia's inertia.get() here
    },
    methods: {
        addQuestion() {
            // Send a request to your API to add a new question with this.newQuestionText
            // After adding, refresh the list of questions
            // You can use Inertia's inertia.post() here
        },
        editQuestion(question) {
            // Implement the edit functionality to update a question
            // You can open a modal or redirect to an edit page
        },
        deleteQuestion(question) {
            // Implement the delete functionality to delete a question
            // You can confirm the delete action and then remove the question from the list
        },
    },
};
</script>

<style scoped>

</style>
