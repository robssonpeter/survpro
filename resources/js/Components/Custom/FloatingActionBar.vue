<template>
    <div v-if="isFocused" class="fixed right-4 bottom-4 z-10">
        <div class="bg-white p-4 rounded shadow-md">
            <button @click="addQuestion" class="block mb-2 px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">
                Add Question
            </button>
            <button @click="duplicateQuestion" class="block mb-2 px-4 py-2 rounded bg-green-500 text-white hover:bg-green-600">
                Duplicate Question
            </button>
            <button @click="deleteQuestion" class="block px-4 py-2 rounded bg-red-500 text-white hover:bg-red-600">
                Delete Question
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isFocused: Boolean, // Indicates if a question is focused or being edited
    },
    methods: {
        addQuestion() {
            // Handle adding a new question
        },
        duplicateQuestion() {
            // Handle duplicating the current question
        },
        deleteQuestion() {
            // Handle deleting the current question
        },
    },
};
</script>
